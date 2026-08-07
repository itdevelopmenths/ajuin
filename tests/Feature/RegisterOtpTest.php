<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\EmailOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterOtpTest extends TestCase
{
    use RefreshDatabase;

    private function extractCode(EmailOtpNotification $n): string
    {
        $ref = new \ReflectionProperty($n, 'code');

        return $ref->getValue($n);
    }

    public function test_register_creates_inactive_user_and_sends_otp(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name'                  => 'Budi',
            'email'                 => 'budi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('otp.verify'));

        $user = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertFalse($user->is_active);
        $this->assertNull($user->email_verified_at);
        $this->assertDatabaseHas('otp_verifications', ['user_id' => $user->id]);

        Notification::assertSentTo($user, EmailOtpNotification::class);
    }

    public function test_correct_otp_verifies_email_but_account_stays_inactive(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name'                  => 'Budi',
            'email'                 => 'budi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'budi@example.com')->firstOrFail();

        $code = null;
        Notification::assertSentTo($user, EmailOtpNotification::class, function (EmailOtpNotification $n) use (&$code) {
            $code = $this->extractCode($n);

            return true;
        });

        $response = $this->post('/verify-otp', ['code' => $code]);

        $response->assertRedirect(route('login'));
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertFalse($user->is_active); // masih tunggu aktivasi admin
        $this->assertDatabaseMissing('otp_verifications', ['user_id' => $user->id]);
    }

    public function test_wrong_otp_is_rejected_and_counts_attempt(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name'                  => 'Budi',
            'email'                 => 'budi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'budi@example.com')->firstOrFail();

        $response = $this->from(route('otp.verify'))->post('/verify-otp', ['code' => '000000']);

        $response->assertRedirect(route('otp.verify'));
        $response->assertSessionHasErrors('code');
        $user->refresh();
        $this->assertNull($user->email_verified_at);
        $this->assertDatabaseHas('otp_verifications', ['user_id' => $user->id, 'attempts' => 1]);
    }
}
