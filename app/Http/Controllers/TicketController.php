<?php

namespace App\Http\Controllers;

use App\Exports\TicketsExport;
use App\Models\MaintenanceType;
use App\Models\Store;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        return view('tickets.index', [
            'statuses' => config('ajuin.statuses'),
            'types'    => config('ajuin.ticket_types'),
            'stores'   => $this->visibleStores($request),
            'spvUsers' => $this->spvUsers($request),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = $this->baseQuery($request);

        return DataTables::eloquent($query)
            ->addColumn('store_name',    fn (Ticket $t) => $t->store?->name ?? '—')
            ->editColumn('ticket_number', fn (Ticket $t) =>
                '<a class="ticket-link" href="' . route('tickets.show', $t) . '">' . $t->ticket_number . '</a>'
            )
            ->editColumn('type', fn (Ticket $t) => config('ajuin.ticket_types')[$t->type] ?? $t->type)
            ->editColumn('status', fn (Ticket $t) =>
                '<span class="badge badge-' . $t->status . '">' . (config('ajuin.statuses')[$t->status] ?? $t->status) . '</span>'
            )
            ->addColumn('deadline', fn (Ticket $t) => $this->renderDeadlineBadge($t))
            ->editColumn('created_at', fn (Ticket $t) => $t->created_at->format('d M Y H:i'))
            ->addColumn('handler_name', fn (Ticket $t) => $t->handler?->name ?? '—')
            ->addColumn('action', fn (Ticket $t) =>
                '<a class="btn-dt-action" href="' . route('tickets.show', $t) . '">Detail</a>'
            )
            ->rawColumns(['ticket_number', 'status', 'deadline', 'action'])
            ->toJson();
    }

    public function export(Request $request): BinaryFileResponse
    {
        Gate::authorize('ticket.export');

        $query = $this->baseQuery($request);
        $filename = 'tickets-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new TicketsExport($query), $filename);
    }

    public function create(Request $request): View
    {
        return view('tickets.create', [
            'stores'           => $this->visibleStores($request),
            'maintenanceTypes' => MaintenanceType::query()->with('tier')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $isMaintenance = $request->input('type') === 'MAINTENANCE';

        $data = $request->validate([
            'store_id'     => ['required', 'exists:stores,id'],
            'type'         => ['required', Rule::in(array_keys(config('ajuin.ticket_types')))],
            'maintenance_type_id'    => [Rule::requiredIf($isMaintenance), 'nullable', 'exists:maintenance_types,id'],
            'recommendation_links'   => ['nullable', 'array', 'max:10'],
            'recommendation_links.*' => ['nullable', 'url', 'max:2048'],
            'description'  => ['required', 'string', 'min:10'],
            'attachments'  => ['required', 'array', 'min:1', 'max:5'],
            'attachments.*'=> ['file', $isMaintenance ? 'mimes:jpg,jpeg,png' : 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $store = Store::findOrFail($data['store_id']);
        abort_unless($request->user()->canSeeStore($store), 403);

        $attachments = [];
        foreach ($request->file('attachments') as $file) {
            $attachments[] = $file->store('ticket-attachments', 'public');
        }

        $recommendationLinks = $data['type'] === 'PEMBELIAN_PERALATAN'
            ? array_values(array_filter($data['recommendation_links'] ?? []))
            : [];

        $maintenanceType = $isMaintenance ? MaintenanceType::with('tier')->findOrFail($data['maintenance_type_id']) : null;

        $ticketData = [
            'store_id'     => $data['store_id'],
            'submitted_by' => $request->user()->name,
            'type'         => $data['type'],
            'maintenance_type_id'       => $maintenanceType?->id,
            'maintenance_tier'          => $maintenanceType?->tier?->name,
            'maintenance_deadline_days' => $maintenanceType?->tier?->deadline_days,
            'recommendation_links' => $recommendationLinks !== [] ? $recommendationLinks : null,
            'description'  => $data['description'],
            'ticket_number'=> Ticket::nextNumber(),
            'source'       => 'HRGA_INITIATIVE',
            'status'       => 'SCREENING',
            'handled_by'   => $request->user()->id,
            'attachments'  => $attachments,
        ];

        $ticket = Ticket::create($ticketData);

        $ticket->logs()->create([
            'user_id'   => $request->user()->id,
            'to_status' => 'SCREENING',
            'note'      => 'Ticket dibuat dari dashboard internal.',
        ]);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket berhasil dibuat.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $ticket->load(['store', 'logs.user', 'handler']);
        abort_unless($request->user()->canSeeStore($ticket->store), 403);

        return view('tickets.show', [
            'ticket'       => $ticket,
            'nextStatuses' => $this->allowedStatuses($request, $ticket),
        ]);
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $ticket->load('store');
        abort_unless($request->user()->canSeeStore($ticket->store), 403);

        $nextStatuses = $this->allowedStatuses($request, $ticket);
        $isCompleting = $request->input('status') === 'SELESAI';
        $isPaying = $request->input('status') === 'PEMBAYARAN';

        $data = $request->validate([
            'status' => ['required', Rule::in($nextStatuses)],
            'note'   => [Rule::requiredIf(fn () => $request->input('status') === 'REJECTED'), 'nullable', 'string'],
            'payment_amount' => [Rule::requiredIf($isPaying), 'nullable', 'integer', 'min:0'],
            'completion_attachments'   => [Rule::requiredIf($isCompleting), 'array', 'min:1', 'max:5'],
            'completion_attachments.*' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $ticketUpdate = [
            'status'      => $data['status'],
            'handled_by'  => $request->user()->id,
            'resolved_at' => in_array($data['status'], Ticket::FINAL_STATUSES, true) ? now() : $ticket->resolved_at,
        ];

        if ($isPaying) {
            $ticketUpdate['payment_amount'] = $data['payment_amount'];
        }

        if ($isCompleting) {
            $completionAttachments = [];
            foreach ($request->file('completion_attachments') as $file) {
                $completionAttachments[] = $file->store('ticket-attachments', 'public');
            }
            $ticketUpdate['completion_attachments'] = $completionAttachments;
        }

        $from = $ticket->status;
        $ticket->fill($ticketUpdate)->save();

        $ticket->logs()->create([
            'user_id'     => $request->user()->id,
            'from_status' => $from,
            'to_status'   => $data['status'],
            'note'        => $data['note'] ?? null,
        ]);

        return back()->with('status', 'Status ticket diperbarui.');
    }

    // ── Private helpers ─────────────────────────────────────────

    /**
     * Render badge deadline maintenance (overdue/segera/normal) untuk kolom DataTables.
     */
    private function renderDeadlineBadge(Ticket $ticket): string
    {
        $deadline = $ticket->maintenanceDeadlineAt();
        if (! $deadline) {
            return '<span style="color:#cbd5e1">—</span>';
        }

        $styles = [
            'overdue' => ['bg' => '#fee2e2', 'fg' => '#b91c1c', 'label' => 'Terlambat'],
            'soon'    => ['bg' => '#fef3c7', 'fg' => '#92400e', 'label' => 'Segera'],
            'ok'      => ['bg' => '#f1f5f9', 'fg' => '#334155', 'label' => null],
        ];
        $style = $styles[$ticket->maintenanceDeadlineStatus()] ?? $styles['ok'];

        $label = $style['label'] ? ' · ' . $style['label'] : '';

        return '<span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;font-weight:700;'
            . 'padding:.2rem .625rem;border-radius:999px;background:' . $style['bg'] . ';color:' . $style['fg'] . '">'
            . $deadline->format('d M Y') . $label . '</span>';
    }

    /**
     * Status tujuan yang boleh dipilih untuk ticket ini.
     * Super Admin bebas pindah ke status mana saja (kecuali status sekarang);
     * role lain mengikuti alur ketat config('ajuin.status_flow').
     */
    private function allowedStatuses(Request $request, Ticket $ticket): array
    {
        if ($request->user()->hasRole('Super Admin')) {
            return array_values(array_diff(Ticket::STATUSES, [$ticket->status]));
        }

        return config('ajuin.status_flow')[$ticket->status] ?? [];
    }

    /**
     * Build base query dengan semua filter request aktif.
     */
    private function baseQuery(Request $request): Builder
    {
        $query = Ticket::query()
            ->with(['store', 'handler'])
            ->visibleTo($request->user());

        // Array-form where() untuk menghindari false positive IDE "Call to unknown function" pada string literal
        $statusInput = $request->input('status');
        if ($statusInput !== null && $statusInput !== '') {
            $query->where(['status' => $statusInput]);
        }
        
        $typeInput = $request->input('type');
        if ($typeInput !== null && $typeInput !== '') {
            $query->where(['type' => $typeInput]);
        }
        
        $storeInput = $request->input('store_id');
        if ($storeInput !== null && $storeInput !== '') {
            $query->where(['store_id' => $storeInput]);
        }
        
        $spvInput = $request->input('spv_id');
        if ($spvInput !== null && $spvInput !== '' && $request->user()->hasRole(['Super Admin', 'Chief'])) {
            $spvUser = User::find($spvInput);
            if ($spvUser) {
                $query->visibleTo($spvUser);
            }
        }
        
        $fromInput = $request->input('from');
        if ($fromInput !== null && $fromInput !== '') {
            $query->whereDate('created_at', '>=', $fromInput);
        }
        
        $toInput = $request->input('to');
        if ($toInput !== null && $toInput !== '') {
            $query->whereDate('created_at', '<=', $toInput);
        }

        return $query;
    }

    /**
     * Toko yang visible untuk user saat ini.
     */
    private function visibleStores(Request $request)
    {
        $sortByName = 'name';
        return Store::query()
            ->visibleTo($request->user())
            ->orderBy($sortByName)
            ->get();
    }

    /**
     * Daftar SPV — hanya tersedia untuk Super Admin.
     */
    private function spvUsers(Request $request)
    {
        if (! $request->user()->hasRole(['Super Admin', 'Chief'])) {
            return collect();
        }

        return User::role(['SPV', 'HRGA', 'Keptok'])->orderBy('name')->get(['id', 'name']);
    }
}
