<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTicketController extends Controller
{
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
