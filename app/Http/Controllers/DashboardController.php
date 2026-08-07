<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Ticket;
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
        if ($spvFilter !== null && $spvFilter !== '' && $request->user()->hasRole('Super Admin')) {
            $base->where('handled_by', $spvFilter);
        }

        $storeList = Store::query()
            ->visibleTo($request->user())
            ->orderBy(column: 'name')
            ->get(['id', 'name', 'code']);

        $spvUsers = $request->user()->hasRole('Super Admin')
            ? User::role(['SPV', 'HRGA', 'Keptok'])->orderBy(column: 'name')->get(['id', 'name'])
            : collect();

        return view('dashboard', [
            'totalToday'   => (clone $base)->whereDate('created_at', today())->count(),
            'totalMonth'   => (clone $base)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'statusCounts' => (clone $base)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'recentTickets' => (clone $base)->with('store')->latest()->limit(10)->get(),
            'stores'       => $storeList,
            'spvUsers'     => $spvUsers,
        ]);
    }
}
