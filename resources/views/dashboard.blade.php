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

{{-- Stat cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:1rem;margin-bottom:1.5rem">
    {{-- Hari ini --}}
    <div class="card card-lift" style="padding:1.25rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem">
            <span style="font-size:.75rem;font-weight:600;color:#64748b;letter-spacing:.02em">Hari ini</span>
            <span class="stat-icon si-indigo">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            </span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#0f172a;letter-spacing:-.03em">{{ $totalToday }}</div>
        <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">ticket masuk</div>
    </div>

    {{-- Bulan ini --}}
    <div class="card card-lift" style="padding:1.25rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem">
            <span style="font-size:.75rem;font-weight:600;color:#64748b;letter-spacing:.02em">Bulan ini</span>
            <span class="stat-icon si-violet">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
            </span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#0f172a;letter-spacing:-.03em">{{ $totalMonth }}</div>
        <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">ticket total</div>
    </div>

    @php
    $statusIcons = [
        'SCREENING'   => ['si-amber',  'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'IN_PROGRESS' => ['si-blue',   'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99'],
        'PEMBAYARAN'  => ['si-amber',  'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z'],
        'EKSEKUSI'    => ['si-violet', 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z'],
        'SELESAI'     => ['si-teal',   'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
        'REJECTED'    => ['si-red',    'M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ];
    @endphp
    @foreach(config('ajuin.statuses') as $key => $label)
    <div class="card card-lift" style="padding:1.25rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem">
            <span class="badge badge-{{ $key }}">{{ $label }}</span>
            <span class="stat-icon {{ $statusIcons[$key][0] ?? 'si-slate' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $statusIcons[$key][1] ?? '' }}"/></svg>
            </span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#0f172a;letter-spacing:-.03em">{{ $statusCounts[$key] ?? 0 }}</div>
        <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">ticket</div>
    </div>
    @endforeach
</div>

{{-- Recent tickets --}}
<div class="card" style="overflow:hidden">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9">
        <h2 style="font-size:1rem;font-weight:700;color:#0f172a">Ticket Terbaru</h2>
        @can('ticket.view')
        <a href="{{ route('tickets.index') }}" style="font-size:.8125rem;font-weight:600;color:#6366f1;text-decoration:none;display:flex;align-items:center;gap:.25rem">
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
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Pengurus</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Status</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-size:.6875rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0">Dibuat</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recentTickets as $ticket)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background .15s" onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background=''">
                    <td style="padding:.875rem 1.5rem">
                        <a href="{{ route('tickets.show', $ticket) }}" style="font-weight:700;color:#6366f1;text-decoration:none;font-size:.8125rem;font-family:monospace">{{ $ticket->ticket_number }}</a>
                    </td>
                    <td style="padding:.875rem 1rem">
                        <div style="font-size:.8125rem;font-weight:600;color:#1e293b">{{ $ticket->store?->name ?? '—' }}</div>
                        <div style="font-size:.75rem;color:#94a3b8;margin-top:1px">{{ $ticket->store?->code ?? '' }}</div>
                    </td>
                    <td style="padding:.875rem 1rem;font-size:.8125rem;color:#64748b">{{ $ticket->handler?->name ?? '—' }}</td>
                    <td style="padding:.875rem 1rem"><span class="badge badge-{{ $ticket->status }}">{{ config('ajuin.statuses')[$ticket->status] }}</span></td>
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
@endsection
