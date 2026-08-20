<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Permission yang tidak boleh diberikan ke role tertentu meski dicentang di UI.
     * Hanya Keptok (dan Super Admin) yang boleh membuat/menambah tiket.
     */
    private const RESTRICTED_ROLE_PERMISSIONS = [
        'SPV'  => ['ticket.create'],
        'HRGA' => ['ticket.create'],
    ];

    private function filterPermissions(string $roleName, array $permissions): array
    {
        $restricted = self::RESTRICTED_ROLE_PERMISSIONS[$roleName] ?? [];

        return array_values(array_diff($permissions, $restricted));
    }

    public function index(): View
    {
        $roles = Role::query()->withCount('users')->with('permissions')->orderBy('name')->get();

        return view('admin.roles.index', [
            'roles' => $roles,
            'permissions' => Permission::query()->orderBy('name')->get()->groupBy(fn (Permission $permission) => str($permission->name)->before('.')->toString()),
            'restrictedPermissions' => $roles->mapWithKeys(
                fn (Role $role) => [$role->name => self::RESTRICTED_ROLE_PERMISSIONS[$role->name] ?? []]
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        Role::create(['name' => $data['name'], 'guard_name' => 'web'])
            ->syncPermissions($this->filterPermissions($data['name'], $data['permissions'] ?? []));

        return back()->with('status', 'Role berhasil dibuat.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->name === 'Super Admin', 403, 'Permission Super Admin tidak bisa diedit via UI.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role)],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($this->filterPermissions($data['name'], $data['permissions'] ?? []));

        return back()->with('status', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->name === 'Super Admin' || $role->users()->exists(), 422, 'Role tidak bisa dihapus.');
        $role->delete();

        return back()->with('status', 'Role berhasil dihapus.');
    }
}
