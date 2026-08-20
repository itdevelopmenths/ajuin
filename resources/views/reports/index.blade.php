@extends('layouts.app', ['title' => 'Laporan'])

@section('content')
<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">Analytics</p>
        <h1 class="page-title" style="margin-top:.25rem">Laporan</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Volume, distribusi, dan lead time berdasarkan scope data aktif.</p>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="padding:1rem 1.25rem;margin-bottom:1.25rem">
    <form method="GET" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.75rem;align-items:end">
        <div>
            <label class="form-label" style="margin:0;font-size:.75rem">Dari</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
        </div>
        <div>
            <label class="form-label" style="margin:0;font-size:.75rem">Sampai</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
        </div>
        <div>
            <label class="form-label" style="margin:0;font-size:.75rem">Toko</label>
            <select id="f-store" name="store_id" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
                <option value="">Semua toko</option>
                @foreach($storeList as $s)
                <option value="{{ $s->id }}" @selected(request('store_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        @if($spvUsers->isNotEmpty())
        <div>
            <label class="form-label" style="margin:0;font-size:.75rem">Pengurus</label>
            <select id="f-spv" name="spv_id" class="form-input" style="padding:.5rem .75rem;font-size:.8125rem">
                <option value="">Semua pengurus</option>
                @foreach($spvUsers as $u)
                <option value="{{ $u->id }}" @selected(request('spv_id') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div style="display:flex;gap:.5rem">
            <button type="submit" class="btn btn-primary" style="padding:.5625rem 1rem">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z"/></svg>
                Filter
            </button>
            @if(request()->anyFilled(['from','to','store_id','spv_id']))
            <a href="{{ route('reports.index') }}" class="btn btn-secondary" style="padding:.5rem .875rem;font-size:.8125rem">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Key metrics --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:1.25rem">
    <div class="card card-lift" style="padding:1.5rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem">
            <span style="font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Total Ticket</span>
            <span class="stat-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z"/></svg>
            </span>
        </div>
        <div style="font-size:2.25rem;font-weight:800;color:#0f172a;letter-spacing:-.03em">{{ $total }}</div>
        <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">dalam periode ini</div>
    </div>

    <div class="card card-lift" style="padding:1.5rem">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.875rem">
            <span style="font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Avg Lead Time</span>
            <span class="stat-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </span>
        </div>
        <div style="font-size:2.25rem;font-weight:800;color:#0f172a;letter-spacing:-.03em">{{ $avgLeadHours }}<span style="font-size:1rem;color:#64748b;font-weight:600">h</span></div>
        <div style="font-size:.75rem;color:#94a3b8;margin-top:.25rem">rata-rata penyelesaian</div>
    </div>
</div>

{{-- Breakdown cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem">
    @php
    $maxStatus = $byStatus->max() ?: 1;
    $maxType   = $byType->max() ?: 1;
    $maxStore  = $byStore->max() ?: 1;
    $maxSpv    = $bySpv->max() ?: 1;
    $statusColors = ['SCREENING'=>'#f59e0b','IN_PROGRESS'=>'#3b82f6','PEMBAYARAN'=>'#f97316','EKSEKUSI'=>'#111827','SELESAI'=>'#14b8a6','REJECTED'=>'#ef4444'];
    @endphp

    {{-- By Status --}}
    <div class="card" style="padding:1.5rem">
        <h3 style="font-size:.9375rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem">Per Status</h3>
        <div style="display:flex;flex-direction:column;gap:.75rem">
            @foreach(config('ajuin.statuses') as $key => $label)
            @php $count = $byStatus[$key] ?? 0; @endphp
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem">
                    <span class="badge badge-{{ $key }}" style="font-size:.65rem">{{ $label }}</span>
                    <span style="font-size:.8rem;font-weight:700;color:#1e293b">{{ $count }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:999px;overflow:hidden">
                    <div style="height:100%;width:{{ $maxStatus > 0 ? round($count/$maxStatus*100) : 0 }}%;background:{{ $statusColors[$key] ?? '#111827' }};border-radius:999px;transition:width .4s"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- By Type --}}
    <div class="card" style="padding:1.5rem">
        <h3 style="font-size:.9375rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem">Per Tipe</h3>
        <div style="display:flex;flex-direction:column;gap:.75rem">
            @foreach(config('ajuin.ticket_types') as $key => $label)
            @php $count = $byType[$key] ?? 0; @endphp
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem">
                    <span style="font-size:.8125rem;font-weight:500;color:#374151">{{ $label }}</span>
                    <span style="font-size:.8rem;font-weight:700;color:#1e293b">{{ $count }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:999px;overflow:hidden">
                    <div style="height:100%;width:{{ $maxType > 0 ? round($count/$maxType*100) : 0 }}%;background:#111827;border-radius:999px"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- By Toko --}}
    <div class="card" style="padding:1.5rem">
        <h3 style="font-size:.9375rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem">Per Toko</h3>
        @if($byStore->isEmpty())
        <p style="font-size:.8125rem;color:#94a3b8">Belum ada data pada periode ini.</p>
        @else
        <div style="display:flex;flex-direction:column;gap:.75rem;max-height:260px;overflow-y:auto;padding-right:.25rem">
            @foreach($byStore->sortDesc() as $storeName => $count)
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem">
                    <span style="font-size:.8125rem;font-weight:500;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:170px">{{ $storeName }}</span>
                    <span style="font-size:.8rem;font-weight:700;color:#1e293b">{{ $count }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:999px;overflow:hidden">
                    <div style="height:100%;width:{{ $maxStore > 0 ? round($count/$maxStore*100) : 0 }}%;background:#111827;border-radius:999px"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- By SPV --}}
    <div class="card" style="padding:1.5rem">
        <h3 style="font-size:.9375rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem">Per Pengurus</h3>
        @if($bySpv->isEmpty())
        <p style="font-size:.8125rem;color:#94a3b8">Belum ada ticket yang ditangani pada periode ini.</p>
        @else
        <div style="display:flex;flex-direction:column;gap:.75rem;max-height:260px;overflow-y:auto;padding-right:.25rem">
            @foreach($bySpv->sortDesc() as $spvName => $count)
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.3rem">
                    <span style="font-size:.8125rem;font-weight:500;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:170px">{{ $spvName }}</span>
                    <span style="font-size:.8rem;font-weight:700;color:#1e293b">{{ $count }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:999px;overflow:hidden">
                    <div style="height:100%;width:{{ $maxSpv > 0 ? round($count/$maxSpv*100) : 0 }}%;background:#111827;border-radius:999px"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        $('#f-store').select2({ placeholder: 'Semua toko', allowClear: true });
        $('#f-spv').select2({ placeholder: 'Semua pengurus', allowClear: true });
    });
</script>
@endpush
@endsection
