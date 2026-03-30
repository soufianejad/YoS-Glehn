<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ImpersonationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_impersonation_secures_session_with_hash()
    {
        // Set an app key to ensure hash_hmac works properly
        Config::set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');

        $admin = User::factory()->create(['role' => 'admin']);
        $userToImpersonate = User::factory()->create(['role' => 'student']);

        // Login as admin
        $this->actingAs($admin);

        // Impersonate
        $response = $this->get(route('admin.users.impersonate', $userToImpersonate));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($userToImpersonate);

        // Session should have both ID and hash
        $this->assertTrue(session()->has('impersonating'));
        $this->assertTrue(session()->has('impersonating_hash'));
        $this->assertEquals($admin->id, session('impersonating'));

        // Stop impersonating successfully
        $response = $this->get(route('users.stop-impersonating'));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertAuthenticatedAs($admin);

        // Session keys should be cleared
        $this->assertFalse(session()->has('impersonating'));
        $this->assertFalse(session()->has('impersonating_hash'));
    }

    public function test_stop_impersonating_fails_with_invalid_hash()
    {
        Config::set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');

        $admin = User::factory()->create(['role' => 'admin']);
        $userToImpersonate = User::factory()->create(['role' => 'student']);

        // Suppose someone is logged in as the student (hijacker)
        $this->actingAs($userToImpersonate);

        // They try to spoof the session
        session()->put('impersonating', $admin->id);
        session()->put('impersonating_hash', 'invalid_hash_value');

        // Stop impersonating (attempting to hijack admin session)
        $response = $this->get(route('users.stop-impersonating'));

        $response->assertRedirect(route('admin.users.index'));

        // They should still be the student, not the admin
        $this->assertAuthenticatedAs($userToImpersonate);

        // Session keys should still be cleared
        $this->assertFalse(session()->has('impersonating'));
        $this->assertFalse(session()->has('impersonating_hash'));
    }

    public function test_stop_impersonating_fails_without_hash()
    {
        Config::set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');

        $admin = User::factory()->create(['role' => 'admin']);
        $userToImpersonate = User::factory()->create(['role' => 'student']);

        // Suppose someone is logged in as the student (hijacker)
        $this->actingAs($userToImpersonate);

        // They try to spoof the session
        session()->put('impersonating', $admin->id);
        // No hash provided

        // Stop impersonating
        $response = $this->get(route('users.stop-impersonating'));

        $response->assertRedirect(route('admin.users.index'));

        // They should still be the student, not the admin
        $this->assertAuthenticatedAs($userToImpersonate);

        // Session keys should still be cleared
        $this->assertFalse(session()->has('impersonating'));
    }
}
