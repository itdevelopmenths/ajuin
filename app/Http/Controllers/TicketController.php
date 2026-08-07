<?php

namespace App\Http\Controllers;

use App\Exports\TicketsExport;
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
            ->editColumn('created_at', fn (Ticket $t) => $t->created_at->format('d M Y H:i'))
            ->addColumn('handler_name', fn (Ticket $t) => $t->handler?->name ?? '—')
            ->addColumn('action', fn (Ticket $t) =>
                '<a class="btn-dt-action" href="' . route('tickets.show', $t) . '">Detail</a>'
            )
            ->rawColumns(['ticket_number', 'status', 'action'])
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
            'stores' => $this->visibleStores($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_id'     => ['required', 'exists:stores,id'],
            'submitted_by' => ['required', 'string', 'max:100'],
            'jabatan'      => ['required', 'string', 'max:100'],
            'type'         => ['required', Rule::in(array_keys(config('ajuin.ticket_types')))],
            'description'  => ['required', 'string', 'min:10'],
            'attachments'  => ['required', 'array', 'min:1', 'max:5'],
            'attachments.*'=> ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $store = Store::findOrFail($data['store_id']);
        abort_unless($request->user()->canSeeStore($store), 403);

        $attachments = [];
        foreach ($request->file('attachments') as $file) {
            $attachments[] = $file->store('ticket-attachments', 'public');
        }

        $ticketData = [
            'store_id'     => $data['store_id'],
            'submitted_by' => $data['submitted_by'],
            'jabatan'      => $data['jabatan'],
            'type'         => $data['type'],
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
            'nextStatuses' => config('ajuin.status_flow')[$ticket->status] ?? [],
        ]);
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $ticket->load('store');
        abort_unless($request->user()->canSeeStore($ticket->store), 403);

        $nextStatuses = config('ajuin.status_flow')[$ticket->status] ?? [];
        $data = $request->validate([
            'status' => ['required', Rule::in($nextStatuses)],
            'note'   => [Rule::requiredIf(fn () => $request->input('status') === 'REJECTED'), 'nullable', 'string'],
        ]);

        $from = $ticket->status;
        $ticket->fill([
            'status'      => $data['status'],
            'handled_by'  => $request->user()->id,
            'resolved_at' => in_array($data['status'], Ticket::FINAL_STATUSES, true) ? now() : $ticket->resolved_at,
        ])->save();

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
        if ($spvInput !== null && $spvInput !== '' && $request->user()->hasRole('Super Admin')) {
            $query->where(['handled_by' => $spvInput]);
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
        if (! $request->user()->hasRole('Super Admin')) {
            return collect();
        }

        return User::role(['SPV', 'HRGA', 'Keptok'])->orderBy('name')->get(['id', 'name']);
    }
}
