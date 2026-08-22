<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Ticket;
use App\Models\Tier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $base = Ticket::query()->visibleTo($request->user());

        // Filter opsional — extract ke variabel agar IDE tidak salah baca string sebagai callable
        $storeFilter = $request->input('store_id');
        if ($storeFilter !== null && $storeFilter !== '') {
            $base->where('store_id', $storeFilter);
        }
        $spvFilter = $request->input('spv_id');
        if ($spvFilter !== null && $spvFilter !== '' && $request->user()->hasRole(['Super Admin', 'Chief'])) {
            $spvUser = User::find($spvFilter);
            if ($spvUser) {
                $base->visibleTo($spvUser);
            }
        }

        $storeList = Store::query()
            ->visibleTo($request->user())
            ->orderBy(column: 'name')
            ->get(['id', 'name', 'code']);

        $spvUsers = $request->user()->hasRole(['Super Admin', 'Chief'])
            ? User::role(['SPV', 'HRGA', 'Keptok'])->orderBy(column: 'name')->get(['id', 'name'])
            : collect();

        // Ticket maintenance aktif yang mendekati (≤24 jam lagi) atau sudah melewati deadline
        $activeMaintenanceTickets = (clone $base)
            ->whereNotNull('maintenance_deadline_days')
            ->whereNotIn('status', Ticket::FINAL_STATUSES)
            ->with('store')
            ->get();

        $deadlineTickets = $activeMaintenanceTickets
            ->filter(fn (Ticket $t) => in_array($t->maintenanceDeadlineStatus(), ['soon', 'overdue'], true))
            ->sortBy(fn (Ticket $t) => $t->maintenanceDeadlineAt())
            ->values();

        $deadlineSoonCount = $deadlineTickets->filter(fn (Ticket $t) => $t->maintenanceDeadlineStatus() === 'soon')->count();
        $deadlineOverdueCount = $deadlineTickets->filter(fn (Ticket $t) => $t->maintenanceDeadlineStatus() === 'overdue')->count();

        $slaByTier = $this->slaByTier($base);

        return view('dashboard', [
            'totalToday'   => (clone $base)->whereDate('created_at', today())->count(),
            'totalMonth'   => (clone $base)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'statusCounts' => (clone $base)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'recentTickets' => (clone $base)->with('store')->latest()->limit(10)->get(),
            'stores'       => $storeList,
            'spvUsers'     => $spvUsers,
            'deadlineTickets'      => $deadlineTickets,
            'deadlineSoonCount'    => $deadlineSoonCount,
            'deadlineOverdueCount' => $deadlineOverdueCount,
            'slaByTier'            => $slaByTier,
        ]);
    }

    /**
     * Target (hari, dari master Tier) vs rata-rata aktual penyelesaian ticket maintenance
     * per kategori/tier, untuk menilai apakah SLA tiap kategori "Aman" atau "Tidak Aman".
     *
     * @param \Illuminate\Database\Eloquent\Builder<Ticket> $base
     */
    private function slaByTier($base)
    {
        return Tier::query()->orderBy('name')->get()->map(function (Tier $tier) use ($base) {
            $resolved = (clone $base)
                ->where('maintenance_tier', $tier->name)
                ->whereNotNull('resolved_at')
                ->get(['created_at', 'resolved_at']);

            $actualDays = $resolved->isNotEmpty()
                ? round($resolved->avg(fn (Ticket $t) => $t->created_at->diffInHours($t->resolved_at) / 24), 1)
                : null;

            return [
                'tier'        => $tier->name,
                'target_days' => $tier->deadline_days,
                'actual_days' => $actualDays,
                'count'       => $resolved->count(),
                'is_safe'     => $actualDays === null ? null : $actualDays <= $tier->deadline_days,
            ];
        });
    }
}
