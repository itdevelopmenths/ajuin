@extends('layouts.app', ['title' => 'Reset Password — Ajuin'])

@section('content')
<div style="width:100%;max-width:420px">
    {{-- Logo mark --}}
    <div style="text-align:center;margin-bottom:2rem">
        <span style="display:inline-flex;width:52px;height:52px;border-radius:14px;background:#111827;align-items:center;justify-content:center;font-size:1.375rem;font-weight:900;color:#fff;box-shadow:0 8px 24px rgba(15,23,42,0.4)">A</span>
        <h1 style="margin-top:.875rem;font-size:1.5rem;font-weight:800;letter-spacing:-.025em;color:#0f172a">Reset Password</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Masukkan password baru Anda.</p>
    </div>

    {{-- Card --}}
    <div class="card" style="padding:2rem">
        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}"
                    class="form-input" placeholder="name@ajuin.test" required autofocus autocomplete="email">
            </div>

            <div>
                <label class="form-label" for="password">Password Baru</label>
                <input id="password" name="password" type="password"
                    class="form-input" placeholder="••••••••" required minlength="8" autocomplete="new-password">
            </div>

            <div>
                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                    class="form-input" placeholder="••••••••" required minlength="8" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;font-size:.9375rem;margin-top:.5rem">
                Reset Password
            </button>
        </form>
    </div>

    <p style="text-align:center;margin-top:2rem;font-size:.75rem;color:#94a3b8">
        © {{ date('Y') }} Heaven Scent Indonesia
    </p>
</div>
@endsection
