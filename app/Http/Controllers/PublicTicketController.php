<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicTicketController extends Controller
{
    public function form(): View
    {
        $sortByName = 'name';
        return view('public.submit', [
            'stores' => Store::query()
                ->orderBy($sortByName)
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_id' => ['required', 'exists:stores,id'],
            'submitted_by' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(array_keys(config('ajuin.ticket_types')))],
            'estimated_price' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'description' => ['required', 'string', 'min:10'],
            'attachments' => ['required', 'array', 'min:1', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $store = Store::findOrFail($data['store_id']);

        $attachments = [];
        foreach ($request->file('attachments') as $file) {
            $attachments[] = $file->store('ticket-attachments', 'public');
        }

        $ticket = Ticket::create([
            'store_id' => $store->id,
            'ticket_number' => Ticket::nextNumber(),
            'submitted_by' => $data['submitted_by'],
            'jabatan' => $data['jabatan'],
            'type' => $data['type'],
            'estimated_price' => $data['type'] === 'PEMBELIAN_PERALATAN' ? ($data['estimated_price'] ?? null) : null,
            'source' => 'USER_SUBMISSION',
            'description' => $data['description'],
            'status' => 'SCREENING',
            'attachments' => $attachments,
        ]);

        $ticket->logs()->create(['to_status' => 'SCREENING', 'note' => 'Ticket dibuat dari form publik.']);

        return redirect()->route('public.track', $ticket->ticket_number);
    }

    public function search(Request $request): View|RedirectResponse
    {
        $number = trim($request->query('ticket_number', ''));
        if ($number !== '') {
            $ticket = Ticket::where('ticket_number', $number)->first();
            if ($ticket) {
                return redirect()->route('public.track', $ticket->ticket_number);
            }
            return redirect()->route('public.track.search')->with('error', 'Nomor pengajuan tidak ditemukan. Pastikan Anda memasukkan nomor yang valid.');
        }

        return view('public.track-search');
    }

    public function track(string $number): View
    {
        $ticket = Ticket::query()
            ->with(['store', 'logs.user'])
            ->where(['ticket_number' => $number])
            ->firstOrFail();

        return view('public.track', ['ticket' => $ticket]);
    }
}
