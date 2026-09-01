@extends('layouts.app', ['title' => 'Profil'])

@section('content')
<div style="margin-bottom:1.5rem">
    <p class="eyebrow">Akun Saya</p>
    <h1 class="page-title" style="margin-top:.25rem">Profil</h1>
</div>

<div style="display:grid;grid-template-columns:1fr;gap:1.25rem;max-width:560px">
    {{-- Info akun --}}
    <div class="card" style="padding:1.5rem">
        <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid #f1f5f9">Info Akun</h2>
        <div style="display:grid;gap:1rem">
            <div>
                <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Nama</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b">{{ $user->name }}</div>
            </div>
            <div>
                <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Email</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b">{{ $user->email }}</div>
            </div>
            <div>
                <div style="font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.375rem">Role</div>
                <div style="font-size:.9rem;font-weight:600;color:#1e293b">{{ $user->getRoleNames()->join(', ') ?: '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Ganti password --}}
    <div class="card" style="padding:1.5rem">
        <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:1.25rem;padding-bottom:.875rem;border-bottom:1px solid #f1f5f9">Ganti Password</h2>
        <form method="post" action="{{ route('profile.password.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="form-label" for="current_password">Password Saat Ini</label>
                <input id="current_password" name="current_password" type="password"
                    class="form-input" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <div>
                <label class="form-label" for="password">Password Baru</label>
                <input id="password" name="password" type="password"
                    class="form-input" placeholder="••••••••" required minlength="8" autocomplete="new-password">
            </div>

            <div>
                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                    class="form-input" placeholder="••••••••" required minlength="8" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>
@endsection
