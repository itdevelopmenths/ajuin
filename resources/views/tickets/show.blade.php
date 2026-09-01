@extends('layouts.app', ['title' => $ticket->ticket_number])

@section('content')
@if($ticket->type === 'MAINTENANCE' && $ticket->maintenance_deadline_days)
@php
    $mtDeadline = $ticket->maintenanceDeadlineAt();
    $mtOverdue  = $ticket->isMaintenanceOverdue();
@endphp
<div id="maintenance-deadline-popup" style="position:fixed;inset:0;background:rgba(15,23,42,.72);display:flex;align-items:center;justify-content:center;z-index:1000;padding:1.5rem">
    <div style="background:#fff;border-radius:1.25rem;max-width:480px;width:100%;padding:2.5rem 2rem;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,.35)">
        <div style="width:64px;height:64px;border-radius:50%;background:{{ $mtOverdue ? '#fee2e2' : '#fef3c7' }};display:grid;place-items:center;margin:0 auto 1.25rem">
            <svg style="width:32px;height:32px;color:{{ $mtOverdue ? '#dc2626' : '#d97706' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
        </div>
        <p style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:{{ $mtOverdue ? '#dc2626' : '#d97706' }}">{{ $mtOverdue ? 'Deadline Terlewat' : 'Perhatian Deadline Maintenance' }}</p>
        <h2 style="font-size:1.75rem;font-weight:800;color:#0f172a;margin-top:.5rem">Tier {{ $ticket->maintenance_tier }} — {{ $ticket->maintenance_deadline_days }} Hari</h2>
        <p style="font-size:.9375rem;color:#475569;margin-top:.75rem;line-height:1.6">
            Ticket ini harus selesai paling lambat<br>
            <strong style="color:#0f172a">{{ $mtDeadline->translatedFormat('d F Y, H:i') }}</strong>
        </p>
        @if($mtOverdue)
        <p style="font-size:.875rem;color:#dc2626;font-weight:700;margin-top:.5rem">⚠ Deadline sudah terlewat!</p>
        @endif
        <button type="button" onclick="document.getElementById('maintenance-deadline-popup').remove()" class="btn btn-primary" style="margin-top:1.5rem;width:100%;justify-content:center">
            Mengerti
        </button>
    </div>
</div>
@endif
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
    <div style="display:flex;align-items:center;gap:.625rem">
        @can('ticket.delete')
        <form method="post" action="{{ route('tickets.destroy', $ticket) }}"
              onsubmit="event.preventDefault(); window.confirmAction('Hapus ticket {{ $ticket->ticket_number }}? Tindakan ini tidak bisa dibatalkan.', () => this.submit())">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                Hapus
            </button>
        </form>
        @endcan
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Kembali
        </a>
    </div>
</div>

<div class="ticket-detail-grid">
    {{-- Kolom utama: detail + timeline --}}
    <div class="ticket-detail-col">
        {{-- Detail card --}}
        <div class="card" style="padding:1.5rem">
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
                @if($ticket->maintenance_deadline_days)
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Deadline</div>
                    <div style="font-size:.875rem;font-weight:600;color:{{ $ticket->isMaintenanceOverdue() ? '#dc2626' : '#1e293b' }}">
                        Tier {{ $ticket->maintenance_tier }} · {{ $ticket->maintenanceDeadlineAt()->translatedFormat('d M Y') }}
                    </div>
                </div>
                @endif
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Sumber</div>
                    <div style="font-size:.8125rem;font-weight:500;color:#475569">{{ $ticket->source }}</div>
                </div>
                @if($ticket->payment_amount !== null)
                <div>
                    <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Nominal Pembayaran</div>
                    <div style="font-size:.875rem;font-weight:700;color:#1e293b">Rp {{ number_format($ticket->payment_amount, 0, ',', '.') }}</div>
                </div>
                @endif
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
                        @continue(empty($attachment))
                        @if(\Illuminate\Support\Str::endsWith(strtolower($attachment), ['.jpg', '.jpeg', '.png']))
                            <button type="button" class="attachment-thumb-btn" data-full="{{ Storage::url($attachment) }}" data-label="Lampiran {{ $index + 1 }}" style="display:block;border-radius:.5rem;overflow:hidden;border:1px solid #e2e8f0;width:fit-content;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,0.05);padding:0;background:none;cursor:zoom-in">
                                <img src="{{ Storage::url($attachment) }}" alt="Lampiran {{ $index + 1 }}" style="width:160px;height:160px;object-fit:cover;display:block;transition:transform .2s" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </button>
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

            @if(!empty($ticket->completion_attachments))
            <div style="margin-top:1.5rem">
                <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.5rem">Bukti Selesai</div>
                <div style="display:flex;gap:.75rem;flex-wrap:wrap">
                    @foreach($ticket->completion_attachments as $index => $attachment)
                        @continue(empty($attachment))
                        <button type="button" class="attachment-thumb-btn" data-full="{{ Storage::url($attachment) }}" data-label="Bukti selesai {{ $index + 1 }}" style="display:block;border-radius:.5rem;overflow:hidden;border:1px solid #e2e8f0;width:fit-content;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,0.05);padding:0;background:none;cursor:zoom-in">
                            <img src="{{ Storage::url($attachment) }}" alt="Bukti selesai {{ $index + 1 }}" style="width:160px;height:160px;object-fit:cover;display:block;transition:transform .2s" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        </button>
                    @endforeach
                </div>
            </div>
            @endif
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

    {{-- Kolom samping: aksi status + catatan --}}
    <div class="ticket-detail-col">
        {{-- Update Status --}}
        <div class="card" style="padding:1.5rem">
            <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid #f1f5f9">Update Status</h2>
            @can('ticket.update_status')
                @if($nextStatuses)
                    <form method="post" action="{{ route('tickets.update-status', $ticket) }}" class="space-y-3" id="update-status-form" enctype="multipart/form-data">
                        @csrf @method('PATCH')
                        <div>
                            <label class="form-label" style="margin-bottom:.3rem">Status baru</label>
                            <select name="status" class="form-input" id="update-status-select" required>
                                <option value="" disabled selected>Saat ini: {{ config('ajuin.statuses')[$ticket->status] ?? $ticket->status }}</option>
                                @foreach($nextStatuses as $status)
                                <option value="{{ $status }}">{{ config('ajuin.statuses')[$status] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="payment-amount-field" style="display:none">
                            <label class="form-label" style="margin-bottom:.3rem">Nominal Pembayaran <span style="color:#ef4444">*</span></label>
                            <input type="number" name="payment_amount" id="payment-amount-input" min="0" step="1" class="form-input" placeholder="cth. 500000">
                        </div>
                        <div id="completion-attachments-field" style="display:none">
                            <label class="form-label" style="margin-bottom:.3rem">Bukti Selesai <span style="color:#ef4444">*</span> <span style="font-weight:400;color:#94a3b8">(foto, wajib min. 1 file, bisa lebih dari satu)</span></label>
                            <div class="file-zone" id="completion-file-zone" style="position:relative;border:2px dashed #e2e8f0;border-radius:.625rem;padding:1.25rem;text-align:center;cursor:pointer;transition:border-color .15s,background .15s">
                                <input type="file" name="completion_attachments[]" id="completion-attachments-input" accept="image/*" multiple style="position:absolute;inset:0;opacity:0;cursor:pointer">
                                <div style="font-size:.8125rem;font-weight:600;color:#64748b">Klik atau seret foto ke sini</div>
                                <div style="font-size:.75rem;color:#94a3b8;margin-top:.2rem">JPG/PNG, maks. 2 MB per file, maks. 5 file</div>
                            </div>
                            <div id="completion-file-list" style="margin-top:.5rem;display:none;flex-direction:column;gap:.5rem"></div>
                            <div id="completion-compress-status" style="display:none;margin-top:.5rem;font-size:.8125rem;color:#64748b;font-weight:600">⏳ Mengompres foto…</div>
                            <button type="button" id="completion-add-more-btn" style="display:none;margin-top:.375rem;background:#fff;color:#111827;border:1.5px dashed #111827;border-radius:.625rem;padding:.5rem .875rem;font-size:.8125rem;font-weight:600;cursor:pointer;width:100%;text-align:center">
                                + Tambah Foto Lainnya
                            </button>
                            <button type="button" id="completion-geo-camera-btn" style="margin-top:.375rem;background:#eff6ff;color:#1d4ed8;border:1.5px solid #bfdbfe;border-radius:.625rem;padding:.5rem .875rem;font-size:.8125rem;font-weight:600;cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;gap:.4rem">
                                <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 8.55 5.25h6.9c.71 0 1.386.343 1.723.925m-9.346 0A2.31 2.31 0 0 0 6.673 6.175l-.415.83c-.336.673-.998 1.117-1.746 1.117H3.75A2.25 2.25 0 0 0 1.5 10.372v8.378a2.25 2.25 0 0 0 2.25 2.25h16.5a2.25 2.25 0 0 0 2.25-2.25v-8.378a2.25 2.25 0 0 0-2.25-2.25h-.762c-.748 0-1.41-.444-1.746-1.117l-.415-.83M6.827 6.175 8.55 5.25m6.9 0-1.723.925M12 15.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
                                Ambil Foto (Lokasi &amp; Waktu)
                            </button>
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

        {{-- Catatan --}}
        <div class="card" style="padding:1.5rem">
            <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid #f1f5f9">Catatan</h2>

            @if($ticket->notes->isEmpty())
            <p style="font-size:.8125rem;color:#94a3b8">Belum ada catatan.</p>
            @else
            <div style="display:flex;flex-direction:column;gap:.75rem;margin-bottom:1.25rem">
                @foreach($ticket->notes as $ticketNote)
                <div style="background:#f8fafc;border-radius:.625rem;padding:.75rem 1rem;border:1px solid #f1f5f9">
                    <div style="font-size:.9rem;color:#334155;white-space:pre-line;line-height:1.6">{{ $ticketNote->note }}</div>
                    <div style="margin-top:.5rem;font-size:.75rem;color:#94a3b8">
                        {{ $ticketNote->created_at->format('d M Y H:i') }}
                        @if($ticketNote->user) · <span style="font-weight:600;color:#64748b">{{ $ticketNote->user->name }}</span> @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @can('ticket.update_status')
            <form method="post" action="{{ route('tickets.notes.store', $ticket) }}" class="space-y-3">
                @csrf
                <div>
                    <textarea name="note" rows="3" class="form-input" placeholder="Tambahkan catatan…" required></textarea>
                </div>
                <button class="btn btn-primary" style="justify-content:center">Tambah Catatan</button>
            </form>
            @endcan
        </div>
    </div>
</div>

<div id="attachment-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.85);z-index:2000;align-items:center;justify-content:center;padding:2rem;cursor:zoom-out">
    <button type="button" id="attachment-modal-close" aria-label="Tutup" style="position:absolute;top:1.25rem;right:1.25rem;background:rgba(255,255,255,.12);border:none;color:#fff;width:40px;height:40px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.5rem;line-height:1">&times;</button>
    <img id="attachment-modal-img" src="" alt="" style="max-width:100%;max-height:100%;border-radius:.5rem;box-shadow:0 20px 60px rgba(0,0,0,.5);cursor:default">
</div>

<style>
.ticket-detail-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
    align-items: start;
}
.ticket-detail-col {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    min-width: 0;
}
@media (min-width: 1024px) {
    .ticket-detail-grid { grid-template-columns: 2fr 1fr; }
}
</style>

@push('scripts')
<script src="{{ asset('js/image-compress.js') }}"></script>
<script src="{{ asset('js/geo-camera.js') }}"></script>
<script>
    (function () {
        const modal = document.getElementById('attachment-modal');
        const modalImg = document.getElementById('attachment-modal-img');
        const modalCloseBtn = document.getElementById('attachment-modal-close');
        if (!modal) return;

        function openAttachmentModal(url, label) {
            modalImg.src = url;
            modalImg.alt = label || '';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeAttachmentModal() {
            modal.style.display = 'none';
            modalImg.src = '';
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.attachment-thumb-btn').forEach(btn => {
            btn.addEventListener('click', () => openAttachmentModal(btn.dataset.full, btn.dataset.label));
        });

        modalCloseBtn.addEventListener('click', closeAttachmentModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) closeAttachmentModal(); });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.style.display !== 'none') closeAttachmentModal();
        });
    })();

    (function () {
        const statusSelect = document.getElementById('update-status-select');
        const completionField = document.getElementById('completion-attachments-field');
        const completionInput = document.getElementById('completion-attachments-input');
        const paymentField = document.getElementById('payment-amount-field');
        const paymentInput = document.getElementById('payment-amount-input');
        if (!statusSelect) return;

        function toggleFields() {
            const isCompleting = statusSelect.value === 'SELESAI';
            completionField.style.display = isCompleting ? 'block' : 'none';
            completionInput.required = isCompleting;

            const isPaying = statusSelect.value === 'PEMBAYARAN';
            paymentField.style.display = isPaying ? 'block' : 'none';
            paymentInput.required = isPaying;
        }

        statusSelect.addEventListener('change', toggleFields);
        toggleFields();

        document.getElementById('update-status-form').addEventListener('submit', function (e) {
            if (statusSelect.value === 'SELESAI' && (!completionInput.files || completionInput.files.length === 0)) {
                e.preventDefault();
                alert('Bukti selesai wajib diisi minimal 1 foto.');
            }
        });

        /* ── Bukti selesai: multi-file zone, akumulasi antar seleksi ── */
        const fileZone = document.getElementById('completion-file-zone');
        const fileList = document.getElementById('completion-file-list');
        const addMoreBtn = document.getElementById('completion-add-more-btn');
        const compressStatus = document.getElementById('completion-compress-status');
        const geoCameraBtn = document.getElementById('completion-geo-camera-btn');
        const MAX_FILES = 5;
        let selectedFiles = [];

        function renderFileList() {
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            completionInput.files = dt.files;

            fileList.innerHTML = '';

            if (selectedFiles.length > 0) {
                fileZone.style.display = 'none';
                fileList.style.display = 'flex';

                selectedFiles.forEach((f, index) => {
                    const item = document.createElement('div');
                    item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:.5rem .75rem;background:#fff;border:1.5px solid #e2e8f0;border-radius:.5rem';
                    item.innerHTML = `
                        <span style="font-size:.8125rem;color:#334155;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px">${f.name}</span>
                        <button type="button" class="completion-remove-btn" style="color:#ef4444;background:none;border:none;cursor:pointer;padding:.25rem" title="Hapus file">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    `;
                    item.querySelector('.completion-remove-btn').addEventListener('click', () => {
                        selectedFiles.splice(index, 1);
                        renderFileList();
                    });
                    fileList.appendChild(item);
                });

                addMoreBtn.style.display = selectedFiles.length < MAX_FILES ? 'block' : 'none';
            } else {
                fileZone.style.display = 'block';
                fileList.style.display = 'none';
                addMoreBtn.style.display = 'none';
            }
        }

        async function addFiles(fileListLike) {
            const files = Array.from(fileListLike);
            if (files.length === 0) return;

            completionInput.disabled = true;
            addMoreBtn.disabled = true;
            if (compressStatus) compressStatus.style.display = 'block';

            for (const newFile of files) {
                if (selectedFiles.length >= MAX_FILES) break;
                const isDuplicate = selectedFiles.some(f => f.name === newFile.name && f.size === newFile.size);
                if (isDuplicate) continue;

                let toAdd = newFile;
                if (window.compressImage) {
                    try {
                        toAdd = await window.compressImage(newFile);
                    } catch (err) {
                        console.error('Gagal mengompres foto, memakai file asli:', err);
                    }
                }
                selectedFiles.push(toAdd);
                renderFileList();
            }

            if (compressStatus) compressStatus.style.display = 'none';
            completionInput.disabled = false;
            addMoreBtn.disabled = false;
        }

        completionInput.addEventListener('change', () => {
            if (completionInput.files.length > 0) {
                addFiles(completionInput.files);
            }
        });

        addMoreBtn.addEventListener('click', () => completionInput.click());

        if (geoCameraBtn) {
            geoCameraBtn.addEventListener('click', async () => {
                if (selectedFiles.length >= MAX_FILES) {
                    alert('Maksimal ' + MAX_FILES + ' foto.');
                    return;
                }
                if (!window.openGeoCamera) return;
                const file = await window.openGeoCamera();
                if (file) {
                    await addFiles([file]);
                }
            });
        }

        fileZone.addEventListener('dragover', e => { e.preventDefault(); fileZone.style.borderColor = '#111827'; });
        fileZone.addEventListener('dragleave', () => { fileZone.style.borderColor = ''; });
        fileZone.addEventListener('drop', e => {
            e.preventDefault();
            fileZone.style.borderColor = '';
            if (e.dataTransfer.files.length > 0) {
                addFiles(e.dataTransfer.files);
            }
        });
    })();
</script>
@endpush
@endsection
