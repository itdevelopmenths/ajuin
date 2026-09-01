@extends('layouts.app', ['title' => 'Login — Ajuin'])

@section('content')
<div style="width:100%;max-width:420px">
    {{-- Logo mark --}}
    <div style="text-align:center;margin-bottom:2rem">
        <span style="display:inline-flex;width:52px;height:52px;border-radius:14px;background:#111827;align-items:center;justify-content:center;font-size:1.375rem;font-weight:900;color:#fff;box-shadow:0 8px 24px rgba(15,23,42,0.4)">A</span>
        <h1 style="margin-top:.875rem;font-size:1.5rem;font-weight:800;letter-spacing:-.025em;color:#0f172a">Ajuin</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Heaven Scent Operations Dashboard</p>
    </div>

    {{-- Card --}}
    <div class="card" style="padding:2rem">
        <form method="post" action="{{ route('login.store') }}" class="space-y-4" id="login-form">
            @csrf

            <div>
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                    class="form-input" placeholder="name@ajuin.test" required autofocus autocomplete="email">
            </div>

            <div>
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <label class="form-label" for="password" style="margin-bottom:0">Password</label>
                    <a href="{{ route('password.request') }}" style="font-size:.75rem;font-weight:600;color:#475569;text-decoration:none">Lupa password?</a>
                </div>
                <input id="password" name="password" type="password"
                    class="form-input" style="margin-top:.3125rem" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <div style="display:flex;align-items:center;gap:.5rem">
                <input id="remember" name="remember" type="checkbox" value="1"
                    style="width:15px;height:15px;accent-color:#111827;cursor:pointer">
                <label for="remember" style="font-size:.8125rem;color:#374151;cursor:pointer">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;font-size:.9375rem;margin-top:.5rem">
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                </svg>
                Masuk
            </button>
        </form>
    </div>

    <div style="margin-top:1.5rem; display:grid; gap:.75rem;">
        <a href="{{ route('public.track.search') }}" class="btn" style="background:#fff; color:#475569; border:1px solid #e2e8f0; box-shadow:0 2px 4px rgba(15,23,42,0.05); justify-content:center; padding:.65rem; font-size:.875rem; text-decoration:none;">
            Lacak Pengajuan Anda
        </a>
    </div>

    <p style="text-align:center;margin-top:2rem;font-size:.75rem;color:#94a3b8">
        © {{ date('Y') }} Heaven Scent Indonesia
    </p>
</div>
@endsection
