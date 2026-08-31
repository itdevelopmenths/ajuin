<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $base = Ticket::query()->visibleTo($request->user());

        if ($request->filled('from')) {
            $base->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $base->whereDate('created_at', '<=', $request->input('to'));
        }
        // Array-form where() — IDE tidak salah baca string column sebagai callable
        $storeInput = $request->input('store_id');
        if ($storeInput !== null && $storeInput !== '') {
            $base->where(['store_id' => $storeInput]);
        }
        $spvInput = $request->input('spv_id');
        if ($spvInput !== null && $spvInput !== '' && $request->user()->hasRole(['Super Admin', 'Chief', 'Manager'])) {
            $spvUser = User::find($spvInput);
            if ($spvUser) {
                $base->visibleTo($spvUser);
            }
        }

        // Aggregate — tidak load semua record ke PHP
        $total = (clone $base)->count();
        $totalPaymentAmount = (clone $base)->sum('payment_amount');

        $byStatus = (clone $base)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byType = (clone $base)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        // Satu kali fetch, dipakai untuk semua breakdown (toko, SPV, lead time)
        $ticketsMin = (clone $base)
            ->with(['store:id,name,code'])
            ->select(['id', 'store_id', 'handled_by', 'status', 'created_at', 'resolved_at', 'maintenance_deadline_days'])
            ->get();

        $byStore = $ticketsMin
            ->groupBy(fn (Ticket $t) => $t->store?->name ?? 'Unknown')
            ->map->count();

        // SPV grouping — via handled_by
        $bySpv = (clone $base)
            ->selectRaw('handled_by, count(*) as total')
            ->whereNotNull('handled_by')
            ->groupBy('handled_by')
            ->with('handler:id,name')
            ->get()
            ->mapWithKeys(fn (Ticket $t) => [$t->handler?->name ?? 'Unknown' => $t->total]);

        $avgLeadHours = round(
            $ticketsMin
                ->whereNotNull('resolved_at')
                ->avg(fn (Ticket $t) => $t->created_at->diffInHours($t->resolved_at)) ?? 0,
            1
        );

        // ── Statistik detail per Toko ───────────────────────────────
        $storeStats = $ticketsMin
            ->whereNotNull('store_id')
            ->groupBy('store_id')
            ->map(function ($tickets) {
                $resolved = $tickets->whereNotNull('resolved_at');

                return [
                    'store'          => $tickets->first()->store,
                    'total'          => $tickets->count(),
                    'selesai'        => $tickets->where('status', 'SELESAI')->count(),
                    'proses'         => $tickets->whereNotIn('status', Ticket::FINAL_STATUSES)->count(),
                    'ditolak'        => $tickets->where('status', 'REJECTED')->count(),
                    'overdue'        => $tickets->filter(fn (Ticket $t) => $t->isMaintenanceOverdue())->count(),
                    'avg_lead_hours' => $resolved->isNotEmpty()
                        ? round($resolved->avg(fn (Ticket $t) => $t->created_at->diffInHours($t->resolved_at)), 1)
                        : null,
                ];
            })
            ->filter(fn (array $row) => $row['store'] !== null)
            ->sortByDesc('total')
            ->values();

        // ── Statistik detail per SPV/Pengurus (via handled_by) ──────
        $handlerIds = $ticketsMin->pluck('handled_by')->filter()->unique()->values();
        $handlers = User::query()
            ->whereIn('id', $handlerIds)
            ->withCount('stores')
            ->with('roles:id,name')
            ->get()
            ->keyBy('id');

        $spvStats = $ticketsMin
            ->whereNotNull('handled_by')
            ->groupBy('handled_by')
            ->map(function ($tickets, $handledBy) use ($handlers) {
                $resolved = $tickets->whereNotNull('resolved_at');

                return [
                    'handler'        => $handlers->get((int) $handledBy),
                    'total'          => $tickets->count(),
                    'selesai'        => $tickets->where('status', 'SELESAI')->count(),
                    'proses'         => $tickets->whereNotIn('status', Ticket::FINAL_STATUSES)->count(),
                    'avg_lead_hours' => $resolved->isNotEmpty()
                        ? round($resolved->avg(fn (Ticket $t) => $t->created_at->diffInHours($t->resolved_at)), 1)
                        : null,
                ];
            })
            ->filter(fn (array $row) => $row['handler'] !== null)
            ->sortByDesc('total')
            ->values();

        // orderBy melalui variable — IDE tidak menganggap string sebagai callable
        $sortByName = 'name';
        $storeList = Store::query()
            ->visibleTo($request->user())
            ->orderBy($sortByName)
            ->get(['id', 'name', 'code']);

        $spvUsers = $request->user()->hasRole(['Super Admin', 'Chief', 'Manager'])
            ? User::role(['SPV', 'HRGA', 'Keptok'])->orderBy($sortByName)->get(['id', 'name'])
            : collect();

        return view('reports.index', compact(
            'total', 'totalPaymentAmount', 'byStatus', 'byType', 'byStore', 'bySpv', 'avgLeadHours',
            'storeStats', 'spvStats', 'storeList', 'spvUsers'
        ));
    }
}
