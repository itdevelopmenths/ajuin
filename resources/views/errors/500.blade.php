@extends('layouts.app', ['title' => '500 Server Error'])

@section('content')
<div class="mx-auto max-w-lg px-4 py-20 text-center relative z-10">
    <div class="mb-8 relative inline-block">
        <div class="absolute inset-0 bg-orange-100 rounded-full blur-xl opacity-60"></div>
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-xl border border-orange-50 relative z-10 mx-auto text-orange-500">
            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
    </div>
    
    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-3">Sistem Mengalami Kendala</h1>
    <p class="text-slate-500 text-lg mb-8 leading-relaxed">
        Maaf, terjadi kesalahan internal pada server kami. Tim teknis telah diberi tahu dan sedang berusaha memperbaikinya secepat mungkin.
    </p>

    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ url('/') }}" class="btn btn-primary px-8 py-3 rounded-xl font-semibold shadow-lg shadow-slate-900/20">
            Muat Ulang Halaman
        </a>
    </div>
</div>
@endsection
