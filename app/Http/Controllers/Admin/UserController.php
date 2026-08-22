<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $sortByName = 'name';

        return view('admin.users.index', [
            'users'  => User::query()
                ->with(['roles', 'scopes.store'])
                ->orderBy($sortByName)
                ->get(),
            'roles'  => Role::query()->orderBy($sortByName)->get(),
            'stores' => Store::query()->orderBy($sortByName)->get(['id', 'name', 'code']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->syncAccess($request, $user);

        return back()->with('status', 'User berhasil dibuat.');
    }
    public function edit(User $user): View
    {
        $user->load(['roles', 'scopes']);
        $sortByName = 'name';

        return view('admin.users.edit', [
            'user'   => $user,
            'roles'  => Role::query()->orderBy($sortByName)->get(),
            'stores' => Store::query()->orderBy($sortByName)->get(['id', 'name', 'code']),
        ]);
    }
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        $user->fill([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $this->syncAccess($request, $user);

        return back()->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->hasRole('Super Admin'), 403, 'Super Admin tidak bisa dihapus dari UI.');
        $user->delete();

        return back()->with('status', 'User dinonaktifkan.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user)],
            'password'   => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'roles'      => ['array'],
            'roles.*'    => ['exists:roles,name'],
            'store_ids'  => ['array'],
            'store_ids.*' => ['exists:stores,id'],
        ]);
    }

    /**
     * Sync role dan scope toko untuk user.
     * Scope type hanya ALL (Super Admin, Chief) atau STORE (SPV, dll).
     */
    private function syncAccess(Request $request, User $user): void
    {
        $user->syncRoles($request->input('roles', []));
        $user->scopes()->delete();
        $user->stores()->sync([]);

        $storeIds = $request->input('store_ids', []);

        if ($user->hasRole(['Super Admin', 'Chief'])) {
            $user->scopes()->create(['scope_type' => 'ALL', 'store_id' => null]);
        } elseif (! empty($storeIds)) {
            foreach ($storeIds as $storeId) {
                $user->scopes()->create(['scope_type' => 'STORE', 'store_id' => $storeId]);
            }
            $user->stores()->sync($storeIds);
        }
    }
}
