@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
{{-- Page header --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">Operational Dashboard</p>
        <h1 class="page-title" style="margin-top:.25rem">Dashboard</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Ringkasan ticket sesuai scope data aktif Anda.</p>
    </div>
    @can('ticket.create')
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
        <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Buat Ticket
    </a>
    @endcan
</div>

{{-- Filter --}}
<div class="card" style="padding:1rem 1.25rem;margin-bottom:1.25rem">
    <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.75rem;align-items:end">
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Toko</label>
            <select id="f-store" name="store_id" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
                <option value="">Semua toko</option>
                @foreach($stores as $s)
                <option value="{{ $s->id }}" @selected(request('store_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        @if($spvUsers->isNotEmpty())
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Pengurus</label>
            <select id="f-spv" name="spv_id" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
                <option value="">Semua pengurus</option>
                @foreach($spvUsers as $u)
                <option value="{{ $u->id }}" @selected(request('spv_id') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        @if(request('store_id') || request('spv_id'))
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="width:100%;justify-content:center;padding:.5rem .875rem;font-size:.8rem">Reset Filter</a>
        </div>
        @endif
    </form>
</div>

{{-- Headline stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-bottom:1rem">
    <div class="card card-lift" style="padding:1.5rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <span style="font-size:.75rem;font-weight:600;color:#64748b;letter-spacing:.02em;text-transform:uppercase">Ticket Hari Ini</span>
            <span class="stat-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            </span>
        </div>
        <div style="font-size:2.25rem;font-weight:800;color:#0f172a;letter-spacing:-.03em">{{ $totalToday }}</div>
        <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">ticket masuk hari ini</div>
    </div>

    <div class="card card-lift" style="padding:1.5rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <span style="font-size:.75rem;font-weight:600;color:#64748b;letter-spacing:.02em;text-transform:uppercase">Ticket Bulan Ini</span>
            <span class="stat-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
            </span>
        </div>
        <div style="font-size:2.25rem;font-weight:800;color:#0f172a;letter-spacing:-.03em">{{ $totalMonth }}</div>
        <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">ticket total bulan ini</div>
    </div>

    {{-- Status breakdown, konsolidasi jadi satu card --}}
    <div class="card" style="padding:1.5rem;grid-column:span 2">
        @php $maxStatus = collect($statusCounts)->max() ?: 1; @endphp
        <span style="font-size:.75rem;font-weight:600;color:#64748b;letter-spacing:.02em;text-transform:uppercase;display:block;margin-bottom:1rem">Breakdown Status</span>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem">
            @foreach(config('ajuin.statuses') as $key => $label)
            @php $count = $statusCounts[$key] ?? 0; @endphp
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem">
                    <span class="badge badge-{{ $key }}" style="font-size:.65rem">{{ $label }}</span>
                    <span style="font-size:.8rem;font-weight:700;color:#1e293b">{{ $count }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:999px;overflow:hidden">
                    <div style="height:100%;width:{{ round($count/$maxStatus*100) }}%;background:#111827;border-radius:999px;transition:width .4s"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Deadline tracking --}}
<div class="card" style="padding:1.5rem;margin-bottom:1rem">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem">
        <div>
            <h2 style="font-size:1rem;font-weight:700;color:#0f172a">Menuju Deadline Maintenance</h2>
            <p style="font-size:.8125rem;color:#64748b;margin-top:.125rem">Ticket maintenance aktif yang mendekati atau sudah melewati batas waktu.</p>
        </div>
        <div style="display:flex;gap:.625rem;flex-wrap:wrap">
            <span style="display:inline-flex;align-items:center;gap:.375rem;padding:.375rem .75rem;border-radius:999px;background:#fef3c7;color:#92400e;font-size:.8125rem;font-weight:700">
                <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
                {{ $deadlineSoonCount }} Segera
            </span>
            <span style="display:inline-flex;align-items:center;gap:.375rem;padding:.375rem .75rem;border-radius:999px;background:#fee2e2;color:#b91c1c;font-size:.8125rem;font-weight:700">
                <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
                {{ $deadlineOverdueCount }} Terlambat
            </span>
        </div>
    </div>

    @if($deadlineTickets->isEmpty())
    <div style="text-align:center;padding:2rem 0;color:#94a3b8;font-size:.875rem">
        <svg style="width:32px;height:32px;margin:0 auto .75rem;color:#e2e8f0;display:block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        Tidak ada ticket yang mendekati atau melewati deadline.
    </div>
    @else
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc">
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Nomor</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Toko</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Tier</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Status</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Deadline</th>
                </tr>
            </thead>
            <tbody>
            @foreach($deadlineTickets as $ticket)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s" onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background=''">
                    <td style="padding:.875rem 1rem">
                        <a href="{{ route('tickets.show', $ticket) }}" style="font-weight:700;color:#111827;text-decoration:none;font-size:.8125rem;font-family:monospace">{{ $ticket->ticket_number }}</a>
                    </td>
                    <td style="padding:.875rem 1rem">
                        <div style="font-size:.8125rem;font-weight:600;color:#1e293b">{{ $ticket->store?->name ?? '—' }}</div>
                        <div style="font-size:.75rem;color:#94a3b8;margin-top:1px">{{ $ticket->store?->code ?? '' }}</div>
                    </td>
                    <td style="padding:.875rem 1rem;font-size:.8125rem;color:#64748b">Tier {{ $ticket->maintenance_tier }}</td>
                    <td style="padding:.875rem 1rem"><span class="badge badge-{{ $ticket->status }}">{{ config('ajuin.statuses')[$ticket->status] }}</span></td>
                    <td style="padding:.875rem 1rem;font-size:.8125rem">
                        @include('tickets._deadline-badge', ['ticket' => $ticket])
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Recent tickets --}}
<div class="card" style="overflow:hidden">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9">
        <h2 style="font-size:1rem;font-weight:700;color:#0f172a">Ticket Terbaru</h2>
        @can('ticket.view')
        <a href="{{ route('tickets.index') }}" style="font-size:.8125rem;font-weight:600;color:#111827;text-decoration:none;display:flex;align-items:center;gap:.25rem">
            Lihat semua
            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
        </a>
        @endcan
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc">
                    <th style="padding:.75rem 1.5rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Nomor</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Toko</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Diajukan Oleh</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Status</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Deadline</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Dibuat</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recentTickets as $ticket)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s" onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background=''">
                    <td style="padding:.875rem 1.5rem">
                        <a href="{{ route('tickets.show', $ticket) }}" style="font-weight:700;color:#111827;text-decoration:none;font-size:.8125rem;font-family:monospace">{{ $ticket->ticket_number }}</a>
                    </td>
                    <td style="padding:.875rem 1rem">
                        <div style="font-size:.8125rem;font-weight:600;color:#1e293b">{{ $ticket->store?->name ?? '—' }}</div>
                        <div style="font-size:.75rem;color:#94a3b8;margin-top:1px">{{ $ticket->store?->code ?? '' }}</div>
                    </td>
                    <td style="padding:.875rem 1rem;font-size:.8125rem;color:#64748b">{{ $ticket->submitted_by }}</td>
                    <td style="padding:.875rem 1rem"><span class="badge badge-{{ $ticket->status }}">{{ config('ajuin.statuses')[$ticket->status] }}</span></td>
                    <td style="padding:.875rem 1rem;font-size:.8125rem">
                        @include('tickets._deadline-badge', ['ticket' => $ticket])
                    </td>
                    <td style="padding:.875rem 1rem;font-size:.8125rem;color:#64748b">{{ $ticket->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:2.5rem 1.5rem;text-align:center;color:#94a3b8;font-size:.875rem">
                        <svg style="width:32px;height:32px;margin:0 auto .75rem;color:#e2e8f0;display:block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/></svg>
                        Belum ada ticket.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        $('#f-store').select2({ placeholder: 'Semua toko', allowClear: true });
        $('#f-spv').select2({ placeholder: 'Semua pengurus', allowClear: true });
        $('#f-store, #f-spv').on('change', function () { this.form.submit(); });
    });
</script>
@endpush
@endsection
