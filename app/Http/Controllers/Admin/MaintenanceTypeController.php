<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceType;
use App\Models\Tier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.maintenance-types.index', [
            'maintenanceTypes' => MaintenanceType::query()->with('tier')->orderBy('name')->get(),
            'tiers'            => Tier::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        MaintenanceType::create($this->validated($request));

        return back()->with('status', 'Jenis maintenance berhasil ditambahkan.');
    }

    public function update(Request $request, MaintenanceType $maintenanceType): RedirectResponse
    {
        $maintenanceType->update($this->validated($request));

        return back()->with('status', 'Jenis maintenance berhasil diperbarui.');
    }

    public function destroy(MaintenanceType $maintenanceType): RedirectResponse
    {
        abort_if($maintenanceType->tickets()->exists(), 422, 'Jenis maintenance masih dipakai oleh ticket.');
        $maintenanceType->delete();

        return back()->with('status', 'Jenis maintenance berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'tier_id' => ['required', 'exists:tiers,id'],
        ]);
    }
}
