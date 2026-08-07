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
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()->withCount('users')->with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get()->groupBy(fn (Permission $permission) => str($permission->name)->before('.')->toString()),
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
            ->syncPermissions($data['permissions'] ?? []);

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
        $role->syncPermissions($data['permissions'] ?? []);

        return back()->with('status', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->name === 'Super Admin' || $role->users()->exists(), 422, 'Role tidak bisa dihapus.');
        $role->delete();

        return back()->with('status', 'Role berhasil dihapus.');
    }
}
