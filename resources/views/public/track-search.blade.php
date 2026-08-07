@extends('layouts.app', ['title' => 'Cek Status Pengajuan'])

@section('content')
<div class="mx-auto max-w-lg px-4 py-10 relative">
    <div class="bg-white rounded-2xl shadow-xl p-8" style="box-shadow: 0 20px 40px -15px rgba(15,23,42,0.1); border: 1px solid #f1f5f9;">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full mb-4" style="background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%); color: #6366f1;">
                <svg style="width: 28px; height: 28px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Lacak Pengajuan</h1>
            <p class="text-slate-500 text-sm">Masukkan nomor pengajuan Anda (cth: AJN-20260622-0001) untuk melihat status dan timeline terkini.</p>
        </div>

        @if(session('error'))
        <div class="mb-6 p-4 rounded-xl text-sm font-medium" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; display: flex; gap: 0.5rem; align-items: flex-start;">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        <form action="{{ route('public.track.search') }}" method="GET">
            <div class="mb-5">
                <label for="ticket_number" class="block text-sm font-bold text-slate-700 mb-2">Nomor Pengajuan</label>
                <input type="text" id="ticket_number" name="ticket_number" class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-mono" style="border-color: #cbd5e1; font-size: 1rem; color: #0f172a;" placeholder="AJN-XXXXXXXX-XXXX" required autofocus autocomplete="off">
            </div>

            <button type="submit" class="w-full btn btn-primary py-3 text-base shadow-lg shadow-indigo-500/30" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                Cari Pengajuan
            </button>
        </form>

        <div class="mt-8 text-center border-t border-slate-100 pt-6">
            <p class="text-sm text-slate-500 mb-3">Belum membuat pengajuan?</p>
            <a href="{{ route('public.submit.form') }}" class="inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                Buat Pengajuan Baru &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
