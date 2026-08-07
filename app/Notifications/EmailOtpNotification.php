<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ttl = (int) config('ajuin.otp.ttl_minutes', 10);

        return (new MailMessage())
            ->subject('Kode Verifikasi Ajuin')
            ->greeting('Halo ' . ($notifiable->name ?? '') . ',')
            ->line('Berikut kode verifikasi untuk mengaktifkan akun Ajuin Anda:')
            ->line('**' . $this->code . '**')
            ->line("Kode berlaku selama {$ttl} menit. Jangan bagikan kode ini ke siapa pun.")
            ->line('Jika Anda tidak merasa mendaftar, abaikan email ini.');
    }
}
