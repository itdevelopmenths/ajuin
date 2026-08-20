@extends('layouts.app', ['title' => 'Manajemen Toko'])

@section('content')
{{-- Header --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">Master Data</p>
        <h1 class="page-title" style="margin-top:.25rem">Lokasi</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Kelola daftar toko, gudang, dan mess Heaven Scent.</p>
    </div>
</div>

{{-- Form tambah toko --}}
<div class="card" style="padding:1.25rem;margin-bottom:1rem;position:relative">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
        <h2 style="font-size:.875rem;font-weight:700;color:#0f172a">Tambah Lokasi Baru</h2>
    </div>
    <form method="POST" action="{{ route('admin.stores.store') }}"
          style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem;align-items:end">
        @csrf
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Nama Lokasi</label>
            <input name="name" placeholder="cth. Heaven Scent Surabaya 01" class="form-input" required>
        </div>
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Kode</label>
            <input name="code" placeholder="cth. HS-SBY-01" class="form-input" style="text-transform:uppercase" required>
        </div>
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Kategori</label>
            <select name="category" class="form-input" required>
                @foreach(config('ajuin.store_categories') as $key => $label)
                <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button class="btn btn-primary" style="width:100%">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Lokasi
            </button>
        </div>
    </form>
</div>

{{-- Status alert --}}
@if(session('status'))
<div style="padding:.875rem 1.25rem;border-radius:.5rem;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;font-size:.875rem;margin-bottom:1rem;display:flex;align-items:center;gap:.625rem">
    <svg style="width:16px;height:16px;flex-shrink:0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
    </svg>
    {{ session('status') }}
</div>
@endif

{{-- DataTable --}}
<div class="card" style="padding:1rem">
    <div style="overflow-x:auto">
        <table id="stores-table" style="width:100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Lokasi</th>
                    <th>Kategori</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#stores-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        language: {
            processing: 'Memuat…',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_',
            info: '_START_–_END_ dari _TOTAL_',
            zeroRecords: 'Tidak ada toko.',
            paginate: { previous: '←', next: '→' }
        },
        ajax: '{{ route('admin.stores.data') }}',
        columns: [
            { data: 'code',       name: 'code',       width: '120px' },
            { data: 'name',       name: 'name' },
            { data: 'category',   name: 'category',   width: '110px' },
            { data: 'created_at', name: 'created_at', width: '130px' },
            { data: 'actions',    orderable: false, searchable: false, width: '90px' }
        ]
    });
});
</script>
@endpush
