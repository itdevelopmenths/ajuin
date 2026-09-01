@extends('layouts.app', ['title' => 'Lupa Password — Ajuin'])

@section('content')
<div style="width:100%;max-width:420px">
    {{-- Logo mark --}}
    <div style="text-align:center;margin-bottom:2rem">
        <span style="display:inline-flex;width:52px;height:52px;border-radius:14px;background:#111827;align-items:center;justify-content:center;font-size:1.375rem;font-weight:900;color:#fff;box-shadow:0 8px 24px rgba(15,23,42,0.4)">A</span>
        <h1 style="margin-top:.875rem;font-size:1.5rem;font-weight:800;letter-spacing:-.025em;color:#0f172a">Lupa Password</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Masukkan email Anda, kami akan kirim link reset password.</p>
    </div>

    {{-- Card --}}
    <div class="card" style="padding:2rem">
        <form method="post" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                    class="form-input" placeholder="name@ajuin.test" required autofocus autocomplete="email">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;font-size:.9375rem;margin-top:.5rem">
                Kirim Link Reset
            </button>
        </form>
    </div>

    <div style="margin-top:1.5rem; display:grid; gap:.75rem;">
        <a href="{{ route('login') }}" class="btn" style="background:#fff; color:#475569; border:1px solid #e2e8f0; box-shadow:0 2px 4px rgba(15,23,42,0.05); justify-content:center; padding:.65rem; font-size:.875rem; text-decoration:none;">
            &larr; Kembali ke Login
        </a>
    </div>

    <p style="text-align:center;margin-top:2rem;font-size:.75rem;color:#94a3b8">
        © {{ date('Y') }} Heaven Scent Indonesia
    </p>
</div>
@endsection
