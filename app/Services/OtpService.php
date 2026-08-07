<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    /**
     * Buat kode OTP baru untuk user, simpan hash-nya, kirim via email.
     * Kode lama (jika ada) dibuang — hanya satu kode aktif per user.
     */
    public function send(User $user): void
    {
        $length = (int) config('ajuin.otp.length', 6);
        $code   = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);

        $user->otpVerifications()->delete();
        $user->otpVerifications()->create([
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('ajuin.otp.ttl_minutes', 10)),
        ]);

        $user->notify(new EmailOtpNotification($code));
    }

    /**
     * Cek kode. Return true jika cocok & belum kadaluarsa; hapus kode saat berhasil.
     * Percobaan salah menaikkan counter attempts.
     */
    public function verify(User $user, string $code): bool
    {
        /** @var OtpVerification|null $otp */
        $otp = $user->otpVerifications()->latest('id')->first();

        if (! $otp || $otp->isExpired()) {
            return false;
        }

        if ($otp->attempts >= (int) config('ajuin.otp.max_attempts', 5)) {
            return false;
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');

            return false;
        }

        $otp->delete();

        return true;
    }

    /**
     * Detik tersisa sebelum boleh kirim ulang. 0 = boleh sekarang.
     */
    public function cooldownRemaining(User $user): int
    {
        $otp = $user->otpVerifications()->latest('id')->first();

        if (! $otp) {
            return 0;
        }

        $ready = $otp->created_at->addSeconds((int) config('ajuin.otp.resend_cooldown', 60));

        return max(0, (int) ceil(now()->diffInSeconds($ready, false)));
    }
}
