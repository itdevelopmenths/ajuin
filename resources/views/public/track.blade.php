@extends('layouts.app', ['title' => 'Lacak Pengajuan: ' . $ticket->ticket_number])

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8 relative">
    {{-- Header Action --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-slate-800">Tracking Pengajuan</h1>
        <button id="btn-download" class="btn btn-primary" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 4px 14px rgba(99,102,241,0.3);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Download PNG
        </button>
    </div>

    {{-- Printable Area --}}
    <div id="ticket-card" class="bg-white rounded-2xl relative" style="box-shadow: 0 20px 40px -15px rgba(15,23,42,0.1); border: 1px solid #f1f5f9; overflow: hidden;">
        {{-- Card Header --}}
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 2rem 2rem 3rem 2rem; color: white;">
            <div class="flex justify-between items-start">
                <div>
                    <p style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Nomor Pengajuan</p>
                    <h2 style="font-size: 1.75rem; font-weight: 800; font-family: monospace; letter-spacing: -0.02em; line-height: 1;">{{ $ticket->ticket_number }}</h2>
                </div>
                <div style="text-align: right;">
                    <span style="display: inline-block; padding: 0.35rem 0.85rem; border-radius: 99px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; background: rgba(255,255,255,0.1); backdrop-filter: blur(4px);">
                        {{ config('ajuin.statuses')[$ticket->status] ?? $ticket->status }}
                    </span>
                    <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem;">{{ $ticket->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Content Info --}}
        <div style="background: white; border-radius: 1.5rem 1.5rem 0 0; margin-top: -1.5rem; padding: 2rem; position: relative;">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px dashed #e2e8f0;">
                <div>
                    <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.3rem;">Lokasi Toko</p>
                    <p style="font-size: 0.95rem; font-weight: 600; color: #1e293b;">{{ $ticket->store?->name ?? '—' }}</p>
                    <p style="font-size: 0.8rem; color: #64748b; font-family: monospace; margin-top: 2px;">{{ $ticket->store?->code ?? '' }}</p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.3rem;">Jenis Pengajuan</p>
                    <p style="font-size: 0.95rem; font-weight: 600; color: #1e293b;">{{ config('ajuin.ticket_types')[$ticket->type] ?? $ticket->type }}</p>
                </div>
            </div>

            <div>
                <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Deskripsi</p>
                <div style="font-size: 0.875rem; color: #475569; line-height: 1.6; background: #f8fafc; padding: 1rem; border-radius: 0.75rem;">
                    {!! nl2br(e($ticket->description)) !!}
                </div>
            </div>

            @if(!empty($ticket->attachments))
            <div style="margin-top: 1.5rem;">
                <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Lampiran</p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    @foreach($ticket->attachments as $index => $attachment)
                        @if(\Illuminate\Support\Str::endsWith(strtolower($attachment), ['.jpg', '.jpeg', '.png']))
                            <a href="{{ Storage::url($attachment) }}" target="_blank" style="display:block;border-radius:.5rem;overflow:hidden;border:1px solid #e2e8f0;width:fit-content;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,0.05)">
                                <img src="{{ Storage::url($attachment) }}" alt="Lampiran {{ $index + 1 }}" style="width:160px;height:160px;object-fit:cover;display:block;transition:transform .2s" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                        @else
                            <a href="{{ Storage::url($attachment) }}" target="_blank" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 0.8rem;background:#f1f5f9;color:#475569;font-size:0.8rem;font-weight:600;border-radius:0.5rem;text-decoration:none">
                                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13"/></svg>
                                Lampiran {{ $index + 1 }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Timeline --}}
            <div style="margin-top: 2.5rem;">
                <p style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Timeline Status</p>
                <div style="position: relative; margin-left: 0.5rem;">
                    <div style="position: absolute; left: 0.45rem; top: 0.5rem; bottom: 0.5rem; width: 2px; background: #e2e8f0;"></div>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1.25rem;">
                        @foreach($ticket->logs as $log)
                        <li style="position: relative; padding-left: 2rem;">
                            {{-- Dot indicator --}}
                            <div style="position: absolute; left: 0; top: 0.25rem; width: 1rem; height: 1rem; border-radius: 50%; background: white; border: 3px solid {{ $loop->last ? '#6366f1' : '#cbd5e1' }}; z-index: 1;"></div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.25rem;">
                                <span style="font-size: 0.875rem; font-weight: 700; color: {{ $loop->last ? '#0f172a' : '#64748b' }};">{{ config('ajuin.statuses')[$log->to_status] ?? $log->to_status }}</span>
                                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">{{ $log->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            
                            @if($log->note || $log->user)
                            <div style="font-size: 0.8rem; color: #64748b; background: #f8fafc; padding: 0.5rem 0.75rem; border-radius: 0.5rem; margin-top: 0.5rem; display: inline-block;">
                                @if($log->user)
                                <span style="font-weight: 600; color: #475569;">{{ $log->user->name }}</span>
                                @if($log->note) &middot; @endif
                                @endif
                                <span>{{ $log->note }}</span>
                            </div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            {{-- Footer info --}}
            <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9; text-align: center;">
                <p style="font-size: 0.65rem; color: #94a3b8; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">Ajuin &copy; Heaven Scent {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.getElementById('btn-download').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        const card = document.getElementById('ticket-card');
        
        // Setup state loading
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;
        btn.disabled = true;

        // Jeda sesaat agar DOM siap
        setTimeout(() => {
            html2canvas(card, {
                scale: 2, // Retained high quality
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                // Restore button
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                // Trigger download
                const link = document.createElement('a');
                link.download = `Ajuin-Track-{{ $ticket->ticket_number }}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            }).catch(err => {
                console.error("Gagal men-generate gambar", err);
                alert("Maaf, gagal men-download bukti. Silakan coba lagi.");
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }, 100);
    });
</script>
@endpush
@endsection
