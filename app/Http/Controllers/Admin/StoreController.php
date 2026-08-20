<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class StoreController extends Controller
{
    public function index(): View
    {
        return view('admin.stores.index');
    }

    public function data(): JsonResponse
    {
        $query = Store::query()->select('stores.*');

        return DataTables::eloquent($query)
            ->editColumn('category', fn (Store $store) =>
                '<span class="badge badge-cat-' . $store->category . '">' . (config('ajuin.store_categories')[$store->category] ?? $store->category) . '</span>'
            )
            ->addColumn('actions', fn (Store $store) =>
                '<div style="display:flex;gap:.5rem;align-items:center">' .
                    '<form method="POST" action="' . route('admin.stores.destroy', $store) . '" style="display:inline" onsubmit="event.preventDefault(); window.confirmAction(\'Hapus toko ini?\', () => this.submit())">' .
                        csrf_field() . method_field('DELETE') .
                        '<button type="submit" style="font-size:.75rem;font-weight:600;color:#ef4444;background:none;border:none;cursor:pointer;padding:0">Hapus</button>' .
                    '</form>' .
                '</div>'
            )
            ->editColumn('created_at', fn (Store $store) => $store->created_at->format('d M Y H:i'))
            ->rawColumns(['category', 'actions'])
            ->toJson();
    }

    public function store(Request $request): RedirectResponse
    {
        Store::create($this->validated($request));

        return back()->with('status', 'Toko berhasil dibuat.');
    }

    public function update(Request $request, Store $store): RedirectResponse
    {
        $store->update($this->validated($request, $store));

        return back()->with('status', 'Toko berhasil diperbarui.');
    }

    public function destroy(Store $store): RedirectResponse
    {
        abort_if($store->tickets()->exists(), 422, 'Toko masih memiliki ticket aktif.');
        $store->delete();

        return back()->with('status', 'Toko berhasil dihapus.');
    }

    private function validated(Request $request, ?Store $store = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', Rule::unique('stores', 'code')->ignore($store)],
            'category' => ['required', Rule::in(array_keys(config('ajuin.store_categories')))],
        ]);
    }
}
