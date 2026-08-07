@extends('layouts.app', ['title' => 'Verifikasi OTP — Ajuin'])

@section('content')
<div style="width:100%;max-width:420px">
    {{-- Logo mark --}}
    <div style="text-align:center;margin-bottom:2rem">
        <span style="display:inline-flex;width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#6366f1,#8b5cf6);align-items:center;justify-content:center;font-size:1.375rem;font-weight:900;color:#fff;box-shadow:0 8px 24px rgba(99,102,241,0.4)">A</span>
        <h1 style="margin-top:.875rem;font-size:1.5rem;font-weight:800;letter-spacing:-.025em;color:#0f172a">Verifikasi Email</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:#64748b">Kode dikirim ke <strong>{{ $email }}</strong></p>
    </div>

    {{-- Card --}}
    <div class="card" style="padding:2rem">
        <form method="post" action="{{ route('otp.verify.store') }}" class="space-y-4" id="otp-form">
            @csrf

            <div>
                <label class="form-label" for="code">Kode OTP</label>
                <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*"
                    maxlength="{{ config('ajuin.otp.length', 6) }}"
                    class="form-input" placeholder="Masukkan {{ config('ajuin.otp.length', 6) }} digit"
                    required autofocus autocomplete="one-time-code"
                    style="text-align:center;letter-spacing:.5em;font-size:1.25rem;font-weight:700">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;font-size:.9375rem;margin-top:.5rem">
                Verifikasi
            </button>
        </form>

        <form method="post" action="{{ route('otp.resend') }}" style="margin-top:1rem;text-align:center">
            @csrf
            <button type="submit" id="resend-btn"
                style="background:none;border:none;color:#4f46e5;font-size:.8125rem;font-weight:600;cursor:pointer"
                @disabled($cooldownRemaining > 0)>
                <span id="resend-label" style="{{ $cooldownRemaining > 0 ? 'display:none' : '' }}">Kirim ulang kode</span>
                <span id="resend-timer" style="{{ $cooldownRemaining > 0 ? '' : 'display:none' }};color:#94a3b8"></span>
            </button>
        </form>
    </div>

    <p style="text-align:center;margin-top:1.5rem;font-size:.8125rem;color:#64748b">
        Salah alamat?
        <a href="{{ route('register') }}" style="color:#4f46e5;font-weight:600;text-decoration:none">Daftar ulang</a>
    </p>

    <p style="text-align:center;margin-top:2rem;font-size:.75rem;color:#94a3b8">
        © {{ date('Y') }} Heaven Scent Indonesia
    </p>
</div>

<script>
(function () {
    var remaining = {{ (int) $cooldownRemaining }};
    var btn = document.getElementById('resend-btn');
    var label = document.getElementById('resend-label');
    var timer = document.getElementById('resend-timer');

    function tick() {
        if (remaining <= 0) {
            btn.disabled = false;
            label.style.display = '';
            timer.style.display = 'none';
            timer.textContent = '';
            return;
        }
        btn.disabled = true;
        label.style.display = 'none';
        timer.style.display = '';
        timer.textContent = 'Kirim ulang dalam ' + remaining + ' detik';
        remaining--;
        setTimeout(tick, 1000);
    }

    if (remaining > 0) { tick(); }
})();
</script>
@endsection
