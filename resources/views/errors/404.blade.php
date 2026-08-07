@extends('layouts.app', ['title' => '404 Not Found'])

@section('content')
<div class="mx-auto max-w-lg px-4 py-20 text-center relative z-10">
    <div class="mb-8 relative inline-block">
        <div class="absolute inset-0 bg-indigo-100 rounded-full blur-xl opacity-60"></div>
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-xl border border-indigo-50 relative z-10 mx-auto text-indigo-500">
            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>
    
    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-3">Halaman Tidak Ditemukan</h1>
    <p class="text-slate-500 text-lg mb-8 leading-relaxed">
        Maaf, rute atau halaman yang Anda cari tidak dapat ditemukan. Mungkin URL salah ketik atau halaman sudah dihapus.
    </p>

    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ url('/') }}" class="btn btn-primary px-8 py-3 rounded-xl font-semibold shadow-lg shadow-indigo-500/30">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
