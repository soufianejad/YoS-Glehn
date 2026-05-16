<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Tests\TestCase;

class PasswordResetMagicLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_reset_code_with_magic_link()
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson(route('password.send-code'), [
            'type' => 'email',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        Mail::assertSent(VerificationCodeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && !empty($mail->resetLink);
        });
    }

    public function test_magic_link_verifies_user_and_redirects()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $code = '123456';
        $expiresAt = now()->addMinutes(10);

        $cacheKey = "password_reset_email_test@example.com";
        Cache::put($cacheKey, [
            'code' => $code,
            'expires_at' => $expiresAt
        ], $expiresAt);

        $response = $this->get(route('password.reset.magic', [
            'email' => 'test@example.com',
            'code' => $code,
            'type' => 'email'
        ]));

        $response->assertRedirect(route('password.request', [
            'verified' => 1,
            'type' => 'email',
            'email' => 'test@example.com',
            'phone' => null
        ]));

        $this->assertEquals('test@example.com', session('reset_verified_email'));
    }

    public function test_invalid_magic_link_fails()
    {
        $response = $this->get(route('password.reset.magic', [
            'email' => 'wrong@example.com',
            'code' => 'wrongcode',
            'type' => 'email'
        ]));

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHasErrors('verification');
    }

    public function test_admin_can_send_reset_link()
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->actingAs($admin)->post(route('admin.users.send-reset-link', $user));

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        Mail::assertSent(VerificationCodeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && !empty($mail->resetLink);
        });

        $this->assertTrue(Cache::has("password_reset_email_user@example.com"));
    }
}
