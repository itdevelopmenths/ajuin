<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    private const SESSION_KEY = 'otp_user_id';

    public function __construct(private readonly OtpService $otp)
    {
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Akun dibuat non-aktif & belum terverifikasi.
        // Aktivasi + assign role dilakukan admin setelah OTP diverifikasi.
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_active' => false,
        ]);

        $this->otp->send($user);

        $request->session()->put(self::SESSION_KEY, $user->id);

        return redirect()->route('otp.verify');
    }

    public function showVerify(Request $request): RedirectResponse|View
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', [
            'email'             => $this->maskEmail($user->email),
            'cooldownRemaining' => $this->otp->cooldownRemaining($user),
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('register');
        }

        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        if (! $this->otp->verify($user, $data['code'])) {
            throw ValidationException::withMessages([
                'code' => 'Kode salah atau sudah kadaluarsa.',
            ]);
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->route('login')->with(
            'status',
            'Email terverifikasi. Akun Anda menunggu aktivasi admin sebelum bisa login.'
        );
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('register');
        }

        $remaining = $this->otp->cooldownRemaining($user);

        if ($remaining > 0) {
            throw ValidationException::withMessages([
                'code' => "Tunggu {$remaining} detik sebelum kirim ulang kode.",
            ]);
        }

        $this->otp->send($user);

        return back()->with('status', 'Kode verifikasi baru sudah dikirim ke email Anda.');
    }

    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get(self::SESSION_KEY);

        return $id ? User::find($id) : null;
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email, 2);
        $visible = mb_substr($name, 0, 2);

        return $visible . str_repeat('*', max(1, mb_strlen($name) - 2)) . '@' . $domain;
    }
}
