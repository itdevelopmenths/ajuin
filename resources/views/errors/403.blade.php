@extends('layouts.app', ['title' => '403 Forbidden'])

@section('content')
<div class="mx-auto max-w-lg px-4 py-20 text-center relative z-10">
    <div class="mb-8 relative inline-block">
        <div class="absolute inset-0 bg-red-100 rounded-full blur-xl opacity-60"></div>
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-xl border border-red-50 relative z-10 mx-auto text-red-500">
            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>
    </div>
    
    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-3">Akses Ditolak</h1>
    <p class="text-slate-500 text-lg mb-8 leading-relaxed">
        Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Hal ini mungkin terjadi karena batasan role akun Anda.
    </p>

    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ url('/') }}" class="btn btn-primary px-8 py-3 rounded-xl font-semibold shadow-lg shadow-slate-900/20">
            Kembali ke Beranda
        </a>
        <a href="javascript:history.back()" class="btn bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-8 py-3 rounded-xl font-semibold transition-colors">
            Halaman Sebelumnya
        </a>
    </div>
</div>
@endsection
