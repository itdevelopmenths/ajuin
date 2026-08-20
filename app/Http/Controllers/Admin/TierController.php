<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TierController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Tier::create($this->validated($request));

        return back()->with('status', 'Tier berhasil ditambahkan.');
    }

    public function update(Request $request, Tier $tier): RedirectResponse
    {
        $tier->update($this->validated($request, $tier));

        return back()->with('status', 'Tier berhasil diperbarui.');
    }

    public function destroy(Tier $tier): RedirectResponse
    {
        abort_if($tier->maintenanceTypes()->exists(), 422, 'Tier masih dipakai oleh jenis maintenance.');
        $tier->delete();

        return back()->with('status', 'Tier berhasil dihapus.');
    }

    private function validated(Request $request, ?Tier $tier = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:20', Rule::unique('tiers', 'name')->ignore($tier)],
            'deadline_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);
    }
}
