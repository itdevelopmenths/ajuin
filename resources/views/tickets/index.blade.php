@extends('layouts.app', ['title' => 'Tickets'])

@section('content')
{{-- Page header --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">Server-side DataTables</p>
        <h1 class="page-title" style="margin-top:.25rem">Daftar Ticket</h1>
    </div>
    <div style="display:flex;align-items:center;gap:.625rem;flex-wrap:wrap">
        @can('ticket.export')
        <a id="export-btn" href="{{ route('tickets.export') }}" class="btn btn-secondary" target="_blank">
            <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Export Excel
        </a>
        @endcan
        @can('ticket.create')
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Buat Ticket
        </a>
        @endcan
    </div>
</div>

{{-- Filter bar --}}
<div class="card" style="padding:1rem 1.25rem;margin-bottom:1rem">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:.75rem;align-items:end">
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Status</label>
            <select id="f-status" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
                <option value="">Semua</option>
                @foreach($statuses as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Tipe</label>
            <select id="f-type" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
                <option value="">Semua</option>
                @foreach($types as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Toko</label>
            <select id="f-store" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
                <option value="">Semua toko</option>
                @foreach($stores as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        @if($spvUsers->isNotEmpty())
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Pengurus</label>
            <select id="f-spv" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
                <option value="">Semua Pengurus</option>
                @foreach($spvUsers as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Dari</label>
            <input type="date" id="f-from" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
        </div>
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Sampai</label>
            <input type="date" id="f-to" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
        </div>
        <div style="display:flex;align-items:flex-end">
            <button id="f-reset" class="btn btn-secondary" style="padding:.5rem .875rem;font-size:.8rem;width:100%">
                <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                Reset
            </button>
        </div>
    </div>
</div>

{{-- DataTable --}}
<div class="card" style="overflow:hidden;padding:1rem">
    <table id="tickets-table" style="width:100%">
        <thead>
            <tr>
                <th>No. Ticket</th>
                <th>Toko</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Handler</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<style>
    .ticket-link { font-weight:700; color:#6366f1; text-decoration:none; font-family:monospace; font-size:.8125rem; }
    .ticket-link:hover { color:#4f46e5; text-decoration:underline; }
    .btn-dt-action {
        display:inline-flex; align-items:center; padding:.3rem .75rem;
        border-radius:.375rem; font-size:.75rem; font-weight:600;
        background:#f5f3ff; color:#6366f1; text-decoration:none; border:1px solid #ede9fe;
        transition:background .15s;
    }
    .btn-dt-action:hover { background:#ede9fe; }
</style>
@endsection

@push('scripts')
<script>
$(function () {
    function getFilters() {
        return {
            status:   $('#f-status').val(),
            type:     $('#f-type').val(),
            store_id: $('#f-store').val(),
            spv_id:   $('#f-spv').val() || '',
            from:     $('#f-from').val(),
            to:       $('#f-to').val(),
        };
    }

    const table = $('#tickets-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        order: [[7, 'desc']],
        language: {
            processing: 'Memuat…',
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_',
            info: '_START_–_END_ dari _TOTAL_',
            zeroRecords: 'Tidak ada ticket.',
            paginate: { previous: '←', next: '→' }
        },
        ajax: {
            url: '{{ route('tickets.data') }}',
            data: d => Object.assign(d, getFilters()),
        },
        columns: [
            { data: 'ticket_number', name: 'ticket_number' },
            { data: 'store_name',    name: 'store.name' },
            { data: 'type',          name: 'type' },
            { data: 'status',        name: 'status' },
            { data: 'handler_name',  name: 'handler.name', orderable: false },
            { data: 'created_at',    name: 'created_at' },
            { data: 'action',        orderable: false, searchable: false },
        ]
    });

    // Filter on change
    $('#f-status,#f-type,#f-store,#f-spv,#f-from,#f-to').on('change', () => table.ajax.reload());

    // Reset
    $('#f-reset').on('click', function () {
        $('#f-status,#f-type,#f-store,#f-spv').val('');
        $('#f-from,#f-to').val('');
        table.search('').ajax.reload();
    });

    // Export button — sync filter params ke URL
    $('#export-btn').on('click', function (e) {
        e.preventDefault();
        const p = new URLSearchParams(getFilters());
        // hapus param kosong
        [...p.entries()].forEach(([k,v]) => { if (!v) p.delete(k); });
        const url = '{{ route('tickets.export') }}?' + p.toString();
        window.open(url, '_blank');
    });
});
</script>
@endpush
