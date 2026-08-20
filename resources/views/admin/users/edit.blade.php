@extends('layouts.app', ['title' => 'Edit User'])

@section('content')
{{-- Header --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">Administrasi</p>
        <h1 class="page-title" style="margin-top:.25rem">Edit User: {{ $user->name }}</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Perbarui info profil, role, dan alokasi toko.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        Kembali ke Daftar
    </a>
</div>

{{-- Status alert --}}
@if(session('status'))
<div style="padding:.875rem 1.25rem;border-radius:.5rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:.875rem;margin-bottom:1rem;display:flex;align-items:center;gap:.625rem">
    <svg style="width:16px;height:16px;flex-shrink:0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
    {{ session('status') }}
</div>
@endif

<div class="card" style="padding:0;overflow:hidden">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')
        
        {{-- Info Dasar --}}
        <div style="display:grid;grid-template-columns:1fr;gap:1.25rem;padding:1.5rem;border-bottom:1px solid #f1f5f9">
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0">Informasi Dasar</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;align-items:end">
                <div>
                    <label style="display:block;font-size:.7rem;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:.3rem">Nama</label>
                    <input name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                </div>
                <div>
                    <label style="display:block;font-size:.7rem;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:.3rem">Email</label>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                </div>
                <div>
                    <label style="display:block;font-size:.7rem;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:.3rem">Password Baru</label>
                    <input name="password" type="password" placeholder="Kosongkan jika tidak diubah" class="form-input">
                </div>
                <div style="display:flex;align-items:center;gap:.5rem;padding-bottom:.5rem">
                    <input type="checkbox" id="active" name="is_active" value="1" @checked(old('is_active', $user->is_active)) style="width:16px;height:16px;accent-color:#111827">
                    <label for="active" style="font-size:.875rem;font-weight:600;color:#374151;cursor:pointer">Akun Aktif</label>
                </div>
            </div>
        </div>

        {{-- Role & Toko --}}
        <div class="role-store-grid" style="display:grid;grid-template-columns:1fr;gap:1.5rem;padding:1.5rem">
            {{-- Role --}}
            <div>
                <p style="font-size:.7rem;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:.625rem">Role</p>
                <div style="display:flex;flex-direction:column;gap:.5rem">
                    @foreach($roles as $role)
                    <label style="display:flex;align-items:center;gap:.5rem;font-size:.875rem;cursor:pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                               @checked(is_array(old('roles')) ? in_array($role->name, old('roles')) : $user->hasRole($role->name))
                               style="width:16px;height:16px;accent-color:#111827">
                        <span>{{ $role->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            
            {{-- Toko --}}
            <div>
                <p style="font-size:.7rem;font-weight:700;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:.625rem">
                    Toko yang Di-assign
                    <span style="font-weight:400;text-transform:none;font-size:.7rem;color:#94a3b8">(kosongkan untuk akses semua bagi Super Admin)</span>
                </p>
                
                {{-- Quick filters / Search --}}
                <div style="margin-bottom:1rem">
                    <input type="text" id="search-store" class="form-input" placeholder="Cari kode atau nama toko..." style="max-width:300px;font-size:.8125rem;padding:.4rem .75rem">
                </div>

                <div id="store-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.5rem .75rem;max-height:300px;overflow-y:auto;padding-right:.25rem;border:1px solid #e2e8f0;padding:1rem;border-radius:.5rem">
                    @foreach($stores as $store)
                    <label class="store-item" style="display:flex;align-items:center;gap:.5rem;font-size:.8125rem;cursor:pointer;padding:.25rem 0">
                        <input type="checkbox" name="store_ids[]" value="{{ $store->id }}"
                               @checked(is_array(old('store_ids')) ? in_array($store->id, old('store_ids')) : $user->scopes->where('store_id', $store->id)->isNotEmpty())
                               style="width:15px;height:15px;accent-color:#111827;flex-shrink:0">
                        <span title="{{ $store->name }}" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            <span class="store-code" style="font-weight:700;color:#374151;font-family:monospace;font-size:.75rem">{{ $store->code }}</span>
                            <span class="store-name" style="color:#64748b;margin-left:.25rem">{{ $store->name }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        
        {{-- Footer Actions --}}
        <div style="padding:1.25rem 1.5rem;background:#f8fafc;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:1rem">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" style="padding-left:2rem;padding-right:2rem">Simpan Perubahan</button>
        </div>
    </form>
</div>

<style>
@media (min-width: 768px) {
    .role-store-grid { grid-template-columns: 1fr 2fr !important; }
}
</style>
@endsection

@push('scripts')
<script>
document.getElementById('search-store').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.store-item');
    
    items.forEach(item => {
        const code = item.querySelector('.store-code').textContent.toLowerCase();
        const name = item.querySelector('.store-name').textContent.toLowerCase();
        if (code.includes(term) || name.includes(term)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endpush
