{{-- $ticket: Ticket --}}
@php
    $deadline = $ticket->maintenanceDeadlineAt();
    $deadlineStatus = $ticket->maintenanceDeadlineStatus();
    $deadlineStyles = [
        'overdue' => ['bg' => '#fee2e2', 'fg' => '#b91c1c', 'label' => 'Terlambat'],
        'soon'    => ['bg' => '#fef3c7', 'fg' => '#92400e', 'label' => 'Segera'],
        'ok'      => ['bg' => '#f1f5f9', 'fg' => '#334155', 'label' => null],
    ];
    $deadlineStyle = $deadlineStyles[$deadlineStatus] ?? null;
@endphp
@if($deadline && $deadlineStyle)
<span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;font-weight:700;padding:.2rem .625rem;border-radius:999px;background:{{ $deadlineStyle['bg'] }};color:{{ $deadlineStyle['fg'] }}">
    {{ $deadline->format('d M Y') }}{{ $deadlineStyle['label'] ? ' · ' . $deadlineStyle['label'] : '' }}
</span>
@else
<span style="color:#cbd5e1">—</span>
@endif
