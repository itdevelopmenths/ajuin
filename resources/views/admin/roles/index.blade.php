@extends('layouts.app', ['title' => 'Role & Permission'])

@section('content')
{{-- Header --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">Administrasi</p>
        <h1 class="page-title" style="margin-top:.25rem">Role &amp; Permission</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Atur peran staf dan hak akses fitur untuk masing-masing role.</p>
    </div>
    @can('role.manage')
    <button onclick="document.getElementById('modal-add-role').style.display='flex'" class="btn btn-primary">
        <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Tambah Role
    </button>
    @endcan
</div>

{{-- Status alert --}}
@if(session('status'))
<div style="padding:.875rem 1.25rem;border-radius:.5rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:.875rem;margin-bottom:1rem;display:flex;align-items:center;gap:.625rem">
    <svg style="width:16px;height:16px;flex-shrink:0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
    {{ session('status') }}
</div>
@endif

{{-- Role list --}}
<div style="display:flex;flex-direction:column;gap:1rem">
    @php $totalPermissions = $permissions->flatten()->count(); @endphp
    @foreach($roles as $role)
    @php
        $isSuperAdmin = $role->name === 'Super Admin';
        $rolePermCount = $role->permissions->count();
        $roleGroups = $role->permissions->map(fn ($p) => str($p->name)->before('.')->toString())->unique();
    @endphp
    <div class="card" style="padding:1.25rem">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap">
            <div>
                <div style="display:flex;align-items:center;gap:.625rem;flex-wrap:wrap">
                    <h2 style="font-size:1.0625rem;font-weight:700;color:#0f172a">{{ $role->name }}</h2>
                    @if($isSuperAdmin)
                    <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.6875rem;font-weight:700;color:#92400e;background:#fef3c7;border-radius:999px;padding:.15rem .625rem">
                        <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                        Terkunci
                    </span>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:.75rem;margin-top:.375rem;font-size:.8125rem;color:#64748b">
                    <span>{{ $role->users_count }} user</span>
                    <span style="color:#e2e8f0">·</span>
                    <span>{{ $rolePermCount }} dari {{ $totalPermissions }} permission</span>
                </div>
                @if($roleGroups->isNotEmpty())
                <div style="display:flex;flex-wrap:wrap;gap:.375rem;margin-top:.625rem">
                    @foreach($roleGroups as $group)
                    <span style="font-size:.6875rem;font-weight:600;color:#475569;background:#f1f5f9;border-radius:999px;padding:.2rem .625rem">{{ config('ajuin.permission_group_labels')[$group] ?? ucfirst($group) }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            @can('role.manage')
            @if(!$isSuperAdmin && $role->users_count === 0)
            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                  onsubmit="event.preventDefault(); window.confirmAction('Hapus role {{ addslashes($role->name) }}?', () => this.submit())">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;cursor:pointer;color:#ef4444;padding:.4rem;border-radius:.375rem" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'" title="Hapus Role">
                    <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
            </form>
            @endif
            @endcan
        </div>

        @if($isSuperAdmin)
        <p style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f1f5f9;font-size:.8125rem;color:#94a3b8">
            Super Admin selalu memiliki semua permission dan tidak bisa diedit lewat halaman ini.
        </p>
        @elseif(auth()->user()->can('role.manage'))
        <details style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f1f5f9">
            <summary style="cursor:pointer;font-size:.8125rem;font-weight:600;color:#111827;list-style:none;display:flex;align-items:center;gap:.375rem;user-select:none">
                <svg class="chevron" style="width:13px;height:13px;transition:transform .15s" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                Kelola nama &amp; permission
            </summary>

            <form method="POST" action="{{ route('admin.roles.update', $role) }}" style="margin-top:1rem">
                @csrf @method('PUT')
                <div style="max-width:320px;margin-bottom:1.25rem">
                    <label class="form-label">Nama Role</label>
                    <input name="name" value="{{ $role->name }}" class="form-input" required>
                </div>

                @include('admin.roles._permission-fields', ['permissions' => $permissions, 'role' => $role, 'restricted' => $restrictedPermissions[$role->name] ?? []])

                <div style="margin-top:1.25rem">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </details>
        @endif
    </div>
    @endforeach
</div>

{{-- Modal tambah role --}}
<div id="modal-add-role" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div class="card" style="width:100%;max-width:680px;max-height:90vh;overflow-y:auto;padding:1.5rem;position:relative">
        <button onclick="document.getElementById('modal-add-role').style.display='none'"
                style="position:absolute;top:1rem;right:1rem;background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1.25rem;line-height:1">✕</button>
        <h2 style="font-size:1.125rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem">Tambah Role Baru</h2>
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div style="max-width:320px;margin-bottom:1.25rem">
                <label class="form-label">Nama Role</label>
                <input name="name" placeholder="cth. Kasir" class="form-input" required>
            </div>

            @include('admin.roles._permission-fields', ['permissions' => $permissions, 'role' => null])

            <div style="display:flex;justify-content:flex-end;gap:.625rem;margin-top:1.25rem">
                <button type="button" onclick="document.getElementById('modal-add-role').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Role</button>
            </div>
        </form>
    </div>
</div>

<style>
details[open] summary .chevron { transform: rotate(90deg); }
summary::-webkit-details-marker { display: none; }
</style>

@push('scripts')
<script>
function toggleGroup(checkbox) {
    const group = checkbox.closest('.perm-group');
    if (!group) return;
    group.querySelectorAll('input[name="permissions[]"]:not(:disabled)').forEach(cb => { cb.checked = checkbox.checked; });
}
</script>
@endpush
@endsection
