@extends('layouts.app', ['title' => 'Manajemen User'])

@section('content')
{{-- Header --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">Administrasi</p>
        <h1 class="page-title" style="margin-top:.25rem">Manajemen User</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Kelola akun, role, dan akses toko per user.</p>
    </div>
    @can('user.create')
    <button onclick="document.getElementById('modal-add-user').style.display='flex'" class="btn btn-primary">
        <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Tambah User
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

{{-- User Table --}}
<div class="card" style="overflow:hidden">
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc">
                    <th style="padding:.75rem 1.5rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">User</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Role</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Akses Toko</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Status</th>
                    <th style="padding:.75rem 1.5rem;text-align:right;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s" onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background=''">
                    <td style="padding:1rem 1.5rem">
                        <div style="font-weight:700;color:#0f172a;font-size:.875rem">{{ $user->name }}</div>
                        <div style="font-size:.8125rem;color:#64748b;margin-top:2px">{{ $user->email }}</div>
                    </td>
                    <td style="padding:1rem">
                        @foreach($user->roles as $role)
                            <span class="badge badge-PENDING" style="background:#f1f5f9;color:#475569">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td style="padding:1rem;font-size:.8125rem;color:#64748b">
                        @if($user->hasRole('Super Admin'))
                            <span style="font-style:italic">Semua Toko</span>
                        @else
                            {{ $user->scopes->where('scope_type', 'STORE')->count() }} Toko
                        @endif
                    </td>
                    <td style="padding:1rem">
                        @if($user->is_active)
                            <span class="badge badge-APPROVED" style="font-size:.65rem">Aktif</span>
                        @else
                            <span class="badge badge-REJECTED" style="font-size:.65rem">Nonaktif</span>
                        @endif
                    </td>
                    <td style="padding:1rem 1.5rem;text-align:right">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:.5rem">
                            @can('user.edit')
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-dt-action" style="padding:.4rem .875rem">Edit / Assign</a>
                            @endcan
                            @can('user.delete')
                            @if(!$user->hasRole('Super Admin'))
                            <button type="button"
                                onclick="window.confirmAction('Hapus user {{ addslashes($user->name) }}?', () => document.getElementById('del-{{ $user->id }}').submit())"
                                style="background:none;border:none;cursor:pointer;color:#ef4444;padding:.4rem;border-radius:.375rem" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'" title="Hapus User">
                                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                            <form id="del-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:none">
                                @csrf @method('DELETE')
                            </form>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding:2.5rem;text-align:center;color:#94a3b8">Belum ada user.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .btn-dt-action {
        display:inline-flex; align-items:center; padding:.3rem .75rem;
        border-radius:.375rem; font-size:.75rem; font-weight:600;
        background:#f1f5f9; color:#111827; text-decoration:none; border:1px solid #e2e8f0;
        transition:background .15s;
    }
    .btn-dt-action:hover { background:#e2e8f0; }
</style>

{{-- Modal tambah user --}}
<div id="modal-add-user" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div class="card" style="width:100%;max-width:600px;padding:1.5rem;position:relative">
        <button onclick="document.getElementById('modal-add-user').style.display='none'"
                style="position:absolute;top:1rem;right:1rem;background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1.25rem;line-height:1">✕</button>
        <h2 style="font-size:1.125rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem">Tambah User Baru</h2>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div style="display:grid;gap:.75rem">
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Nama</label>
                    <input name="name" placeholder="Nama lengkap" class="form-input" required>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Email</label>
                    <input name="email" type="email" placeholder="email@domain.com" class="form-input" required>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Password</label>
                    <input name="password" type="password" placeholder="Min 8 karakter" class="form-input" required>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.5rem">Role</label>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap">
                        @foreach($roles as $role)
                        <label style="display:flex;align-items:center;gap:.375rem;font-size:.875rem;cursor:pointer">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" style="accent-color:#111827">
                            {{ $role->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.5rem">Toko yang Di-assign</label>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.25rem .75rem;max-height:160px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:.5rem;padding:.75rem">
                        @foreach($stores as $store)
                        <label style="display:flex;align-items:center;gap:.375rem;font-size:.75rem;cursor:pointer">
                            <input type="checkbox" name="store_ids[]" value="{{ $store->id }}" style="width:14px;height:14px;accent-color:#111827;flex-shrink:0">
                            <span><b style="font-family:monospace;font-size:.7rem">{{ $store->code }}</b> {{ $store->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:.625rem;margin-top:.5rem">
                    <button type="button" onclick="document.getElementById('modal-add-user').style.display='none'" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat User</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
