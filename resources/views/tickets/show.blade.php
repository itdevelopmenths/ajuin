@extends('layouts.app', ['title' => $ticket->ticket_number])

@section('content')
{{-- Page header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">{{ $ticket->store?->name ?? '—' }} · {{ $ticket->store?->code ?? '' }}</p>
        <h1 class="page-title" style="margin-top:.25rem;font-family:monospace">{{ $ticket->ticket_number }}</h1>
        <div style="display:flex;align-items:center;gap:.5rem;margin-top:.625rem;flex-wrap:wrap">
            <span class="badge badge-{{ $ticket->status }}">{{ config('ajuin.statuses')[$ticket->status] ?? $ticket->status }}</span>
            <span style="font-size:.75rem;color:#94a3b8">{{ $ticket->created_at->format('d M Y H:i') }}</span>
        </div>
    </div>
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
        <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        Kembali
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr;gap:1.25rem">
    {{-- Top row: detail + update status --}}
    <div style="display:grid;grid-template-columns:1fr;gap:1.25rem" class="lg-grid-cols-3">
        {{-- Detail card --}}
        <div class="card" style="padding:1.5rem;grid-column:span 2">
            <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid #f1f5f9">Detail Ticket</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1rem;margin-bottom:1.5rem">
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Status</div>
                    <span class="badge badge-{{ $ticket->status }}">{{ config('ajuin.statuses')[$ticket->status] ?? $ticket->status }}</span>
                </div>
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Tipe</div>
                    <div style="font-size:.875rem;font-weight:600;color:#1e293b">{{ config('ajuin.ticket_types')[$ticket->type] ?? $ticket->type }}</div>
                </div>
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Sumber</div>
                    <div style="font-size:.8125rem;font-weight:500;color:#475569">{{ $ticket->source }}</div>
                </div>
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Diajukan oleh</div>
                    <div style="font-size:.875rem;font-weight:600;color:#1e293b">{{ $ticket->submitted_by }}</div>
                </div>
                @if($ticket->jabatan)
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Jabatan</div>
                    <div style="font-size:.875rem;font-weight:600;color:#1e293b">{{ $ticket->jabatan }}</div>
                </div>
                @endif
                @if($ticket->handler)
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Handler</div>
                    <div style="font-size:.875rem;font-weight:600;color:#1e293b">{{ $ticket->handler->name }}</div>
                </div>
                @endif
            </div>

            <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.5rem">Deskripsi</div>
            <div style="font-size:.9rem;line-height:1.7;color:#334155;white-space:pre-line;background:#f8fafc;border-radius:.625rem;padding:1rem;border:1px solid #f1f5f9">{{ $ticket->description }}</div>

            @if(!empty($ticket->attachments))
            <div style="margin-top:1.5rem">
                <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.5rem">Lampiran</div>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap">
                    @foreach($ticket->attachments as $index => $attachment)
                        @if(\Illuminate\Support\Str::endsWith(strtolower($attachment), ['.jpg', '.jpeg', '.png']))
                            <a href="{{ Storage::url($attachment) }}" target="_blank" style="display:block;border-radius:.5rem;overflow:hidden;border:1px solid #e2e8f0;width:fit-content;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,0.05)">
                                <img src="{{ Storage::url($attachment) }}" alt="Lampiran {{ $index + 1 }}" style="width:160px;height:160px;object-fit:cover;display:block;transition:transform .2s" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                        @else
                            <a href="{{ Storage::url($attachment) }}" target="_blank" class="btn btn-secondary" style="font-size:.8125rem">
                                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                                Lampiran {{ $index + 1 }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Update Status --}}
        <div class="card" style="padding:1.5rem;align-self:start">
            <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid #f1f5f9">Update Status</h2>
            @can('ticket.update_status')
                @if($nextStatuses)
                    <form method="post" action="{{ route('tickets.update-status', $ticket) }}" class="space-y-3" id="update-status-form">
                        @csrf @method('PATCH')
                        <div>
                            <label class="form-label" style="margin-bottom:.3rem">Status baru</label>
                            <select name="status" class="form-input">
                                @foreach($nextStatuses as $status)
                                <option value="{{ $status }}">{{ config('ajuin.statuses')[$status] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="margin-bottom:.3rem">Catatan <span style="color:#94a3b8;font-weight:400">(opsional)</span></label>
                            <textarea name="note" rows="3" class="form-input" placeholder="Tambahkan catatan status…"></textarea>
                        </div>
                        <button class="btn btn-primary" style="width:100%;justify-content:center">Update Status</button>
                    </form>
                @else
                    <div style="text-align:center;padding:1rem 0">
                        <div style="width:44px;height:44px;border-radius:50%;background:#d1fae5;display:grid;place-items:center;margin:0 auto .75rem">
                            <svg style="width:20px;height:20px;color:#059669" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </div>
                        <p style="font-size:.875rem;color:#64748b;font-weight:500">Status sudah final.</p>
                        @if($ticket->resolved_at)
                        <p style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">Selesai {{ $ticket->resolved_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>
                @endif
            @endcan
        </div>
    </div>

    {{-- Timeline --}}
    <div class="card" style="padding:1.5rem">
        <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1.5rem;padding-bottom:.875rem;border-bottom:1px solid #f1f5f9">Timeline</h2>
        <ol class="timeline">
            @foreach($ticket->logs as $log)
            <li class="timeline-item">
                <div class="timeline-dot">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                </div>
                <div style="padding-top:.125rem;flex:1">
                    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
                        @if($log->from_status)
                        <span class="badge badge-{{ $log->from_status }}" style="font-size:.65rem">{{ $log->from_status }}</span>
                        <svg style="width:12px;height:12px;color:#94a3b8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        @endif
                        <span class="badge badge-{{ $log->to_status }}" style="font-size:.65rem">{{ $log->to_status }}</span>
                    </div>
                    <div style="margin-top:.375rem;font-size:.75rem;color:#94a3b8">
                        {{ $log->created_at->format('d M Y H:i') }}
                        @if($log->user) · <span style="font-weight:600;color:#64748b">{{ $log->user->name }}</span> @endif
                    </div>
                    @if($log->note)
                    <div style="margin-top:.375rem;font-size:.8125rem;color:#475569;background:#f8fafc;border-radius:.5rem;padding:.5rem .75rem;border-left:3px solid #e2e8f0">{{ $log->note }}</div>
                    @endif
                </div>
            </li>
            @endforeach
        </ol>
    </div>
</div>

<style>
@media (min-width: 1024px) {
    .lg-grid-cols-3 { grid-template-columns: 1fr 1fr 1fr !important; }
    .lg-grid-cols-3 > :first-child { grid-column: span 2 !important; }
}
</style>
@endsection
