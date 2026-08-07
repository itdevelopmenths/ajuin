@extends('layouts.app', ['title' => 'Daftar — Ajuin'])

@section('content')
<div style="width:100%;max-width:420px">
    {{-- Logo mark --}}
    <div style="text-align:center;margin-bottom:2rem">
        <span style="display:inline-flex;width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#6366f1,#8b5cf6);align-items:center;justify-content:center;font-size:1.375rem;font-weight:900;color:#fff;box-shadow:0 8px 24px rgba(99,102,241,0.4)">A</span>
        <h1 style="margin-top:.875rem;font-size:1.5rem;font-weight:800;letter-spacing:-.025em;color:#0f172a">Buat Akun</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Verifikasi email lewat kode OTP</p>
    </div>

    {{-- Card --}}
    <div class="card" style="padding:2rem">
        <form method="post" action="{{ route('register.store') }}" class="space-y-4" id="register-form">
            @csrf

            <div>
                <label class="form-label" for="name">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                    class="form-input" placeholder="Nama lengkap" required autofocus autocomplete="name">
            </div>

            <div>
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                    class="form-input" placeholder="name@ajuin.test" required autocomplete="email">
            </div>

            <div>
                <label class="form-label" for="password">Password</label>
                <input id="password" name="password" type="password"
                    class="form-input" placeholder="Minimal 8 karakter" required autocomplete="new-password">
            </div>

            <div>
                <label class="form-label" for="password_confirmation">Ulangi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                    class="form-input" placeholder="••••••••" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;font-size:.9375rem;margin-top:.5rem">
                Daftar &amp; Kirim OTP
            </button>
        </form>
    </div>

    <p style="text-align:center;margin-top:1.5rem;font-size:.8125rem;color:#64748b">
        Sudah punya akun?
        <a href="{{ route('login') }}" style="color:#4f46e5;font-weight:600;text-decoration:none">Masuk di sini</a>
    </p>

    <p style="text-align:center;margin-top:2rem;font-size:.75rem;color:#94a3b8">
        © {{ date('Y') }} Heaven Scent Indonesia
    </p>
</div>
@endsection
