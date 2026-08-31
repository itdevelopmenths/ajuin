@extends('layouts.app', ['title' => 'Jenis Maintenance'])

@section('content')
{{-- Header --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <div>
        <p class="eyebrow">Master Data</p>
        <h1 class="page-title" style="margin-top:.25rem">Jenis Maintenance</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Kelola tier deadline, lalu hubungkan tiap jenis maintenance ke tiernya.</p>
    </div>
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

{{-- ═══ Tier ═══ --}}
<p class="eyebrow" style="margin-bottom:.5rem">1. Tier &amp; Deadline</p>
@can('maintenance_type.manage')
<div class="card" style="padding:1.25rem;margin-bottom:1rem">
    <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin-bottom:1rem">Tambah Tier</h2>
    <form method="POST" action="{{ route('admin.tiers.store') }}"
          class="tier-add-grid" style="display:grid;grid-template-columns:1fr;gap:.75rem;align-items:end">
        @csrf
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Nama Tier</label>
            <input name="name" placeholder="cth. A" class="form-input" required>
        </div>
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Deadline (hari)</label>
            <input type="number" name="deadline_days" min="1" max="365" placeholder="3" class="form-input" required>
        </div>
        <div>
            <button class="btn btn-primary" style="width:100%">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Tier
            </button>
        </div>
    </form>
</div>
@endcan

<div class="card" style="padding:1rem;margin-bottom:1.5rem">
    @if($tiers->isEmpty())
    <p style="font-size:.875rem;color:#64748b;text-align:center;padding:1.5rem 0">Belum ada tier. Tambahkan lewat form di atas — contoh: Tier A, B, C.</p>
    @else
    <div style="display:flex;flex-direction:column;gap:.75rem">
        @foreach($tiers as $tier)
        <div class="tier-row-grid" style="display:grid;grid-template-columns:1fr;gap:.75rem;align-items:center;padding:.75rem;border:1px solid #f1f5f9;border-radius:.625rem">
            @can('maintenance_type.manage')
            <form method="POST" action="{{ route('admin.tiers.update', $tier) }}" style="display:contents">
                @csrf @method('PUT')
                <input name="name" value="{{ $tier->name }}" class="form-input" required>
                <input type="number" name="deadline_days" value="{{ $tier->deadline_days }}" min="1" max="365" class="form-input" required>
                <button class="btn btn-secondary" style="white-space:nowrap">Simpan</button>
            </form>
            <form method="POST" action="{{ route('admin.tiers.destroy', $tier) }}"
                  onsubmit="event.preventDefault(); window.confirmAction('Hapus tier ini?', () => this.submit())">
                @csrf @method('DELETE')
                <button type="submit" style="color:#ef4444;background:none;border:none;cursor:pointer;font-size:.8125rem;font-weight:600;padding:.5rem;white-space:nowrap">
                    Hapus
                </button>
            </form>
            @else
            <span style="font-weight:600">{{ $tier->name }}</span>
            <span>{{ $tier->deadline_days }} hari</span>
            @endcan
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ═══ Jenis Maintenance ═══ --}}
<p class="eyebrow" style="margin-bottom:.5rem">2. Jenis Maintenance</p>
@can('maintenance_type.manage')
<div class="card" style="padding:1.25rem;margin-bottom:1rem">
    <h2 style="font-size:.875rem;font-weight:700;color:#0f172a;margin-bottom:1rem">Tambah Jenis Maintenance</h2>
    @if($tiers->isEmpty())
    <p style="font-size:.8125rem;color:#94a3b8">Tambahkan minimal satu tier terlebih dahulu sebelum membuat jenis maintenance.</p>
    @else
    <form method="POST" action="{{ route('admin.maintenance-types.store') }}"
          class="mt-add-grid" style="display:grid;grid-template-columns:1fr;gap:.75rem;align-items:end">
        @csrf
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Nama Jenis</label>
            <input name="name" placeholder="cth. Kerusakan Kaca" class="form-input" required>
        </div>
        <div>
            <label style="display:block;font-size:.75rem;font-weight:600;color:#64748b;margin-bottom:.3rem">Tier</label>
            <select name="tier_id" class="form-input" required>
                <option value="">— Pilih tier —</option>
                @foreach($tiers as $tier)
                <option value="{{ $tier->id }}">Tier {{ $tier->name }} ({{ $tier->deadline_days }} hari)</option>
                @endforeach
            </select>
        </div>
        <div>
            <button class="btn btn-primary" style="width:100%">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah
            </button>
        </div>
    </form>
    @endif
</div>
@endcan

<div class="card" style="padding:1rem">
    @if($maintenanceTypes->isEmpty())
    <p style="font-size:.875rem;color:#64748b;text-align:center;padding:1.5rem 0">Belum ada jenis maintenance. Tambahkan lewat form di atas.</p>
    @else
    <div style="display:flex;flex-direction:column;gap:.75rem">
        @foreach($maintenanceTypes as $item)
        <div class="mt-row-grid" style="display:grid;grid-template-columns:1fr;gap:.75rem;align-items:center;padding:.75rem;border:1px solid #f1f5f9;border-radius:.625rem">
            @can('maintenance_type.manage')
            <form method="POST" action="{{ route('admin.maintenance-types.update', $item) }}" style="display:contents">
                @csrf @method('PUT')
                <input name="name" value="{{ $item->name }}" class="form-input" required>
                <select name="tier_id" class="form-input" required>
                    @foreach($tiers as $tier)
                    <option value="{{ $tier->id }}" @selected($item->tier_id === $tier->id)>Tier {{ $tier->name }} ({{ $tier->deadline_days }} hari)</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary" style="white-space:nowrap">Simpan</button>
            </form>
            <form method="POST" action="{{ route('admin.maintenance-types.destroy', $item) }}"
                  onsubmit="event.preventDefault(); window.confirmAction('Hapus jenis maintenance ini?', () => this.submit())">
                @csrf @method('DELETE')
                <button type="submit" style="color:#ef4444;background:none;border:none;cursor:pointer;font-size:.8125rem;font-weight:600;padding:.5rem;white-space:nowrap">
                    Hapus
                </button>
            </form>
            @else
            <span style="font-weight:600">{{ $item->name }}</span>
            <span>Tier {{ $item->tier?->name }}</span>
            @endcan
        </div>
        @endforeach
    </div>
    @endif
</div>

<style>
@media (min-width: 640px) {
    .tier-add-grid { grid-template-columns: 1fr 1fr auto !important; }
    .tier-row-grid { grid-template-columns: 1fr 1fr auto auto !important; }
    .mt-add-grid { grid-template-columns: 2fr 1fr auto !important; }
    .mt-row-grid { grid-template-columns: 2fr 1fr auto auto !important; }
}
</style>
@endsection
