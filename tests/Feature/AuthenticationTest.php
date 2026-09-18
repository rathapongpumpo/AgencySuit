<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_001_user_can_register_with_email_and_password(): void
    {
        $response = $this->post(route('register.store'), [
            'email' => 'agent@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $response->assertRedirect(route('today'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'agent@example.test']);
        $this->assertTrue(Hash::check('secure-password', User::firstOrFail()->password));
    }

    public function test_auth_002_user_can_log_in_with_email_and_password(): void
    {
        $user = User::factory()->create(['password' => 'secure-password']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'secure-password',
        ])->assertRedirect(route('today'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_auth_003_incorrect_password_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'secure-password']);

        $this->from(route('login'))
            ->post(route('login.store'), ['email' => $user->email, 'password' => 'incorrect-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_auth_004_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));

        $this->assertGuest();
        $this->get(route('today'))->assertRedirect(route('login'));
    }

    public function test_auth_005_guest_cannot_open_the_protected_today_page(): void
    {
        $this->get(route('today'))->assertRedirect(route('login'));
    }

    public function test_auth_006_logged_in_user_can_open_the_protected_today_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('today'))
            ->assertOk()
            ->assertSee('วันนี้');
    }

    public function test_auth_007_user_can_request_a_password_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_auth_008_user_can_reset_a_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }

    public function test_auth_009_duplicate_email_registration_is_rejected(): void
    {
        User::factory()->create(['email' => 'agent@example.test']);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'email' => 'agent@example.test',
                'password' => 'secure-password',
                'password_confirmation' => 'secure-password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_auth_010_google_user_is_created_from_a_valid_callback(): void
    {
        $this->fakeSuccessfulGoogleCallback('new-google-user@example.test', 'google-user-1');

        $this->get(route('google.callback', ['code' => 'valid-code', 'state' => 'valid-state']))
            ->assertRedirect(route('today'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'new-google-user@example.test',
            'provider' => 'google',
            'provider_id' => 'google-user-1',
        ]);
    }

    public function test_auth_011_existing_google_email_does_not_create_a_duplicate_user(): void
    {
        $existingUser = User::factory()->create(['email' => 'existing-google-user@example.test']);
        $this->fakeSuccessfulGoogleCallback($existingUser->email, 'google-user-2');

        $this->get(route('google.callback', ['code' => 'valid-code', 'state' => 'valid-state']))
            ->assertRedirect(route('today'));

        $this->assertDatabaseCount('users', 1);
        $this->assertAuthenticatedAs($existingUser);
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'provider' => 'google',
            'provider_id' => 'google-user-2',
        ]);
    }

    public function test_auth_012_google_oauth_failure_is_handled_without_an_error_page(): void
    {
        $this->get(route('google.callback', ['error' => 'access_denied']))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');

        $this->get(route('google.redirect'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');
    }

    public function test_google_redirect_uses_a_state_value_when_google_is_configured(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        $this->get(route('google.redirect'))
            ->assertRedirectContains('https://accounts.google.com/o/oauth2/v2/auth')
            ->assertSessionHas('auth.google_oauth_state');
    }

    public function test_google_redirect_uri_defaults_to_app_url_when_not_explicitly_configured(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
        ]);

        $expectedRedirect = rtrim((string) config('app.url'), '/').'/auth/google/callback';

        $this->get(route('google.redirect'))
            ->assertRedirectContains(rawurlencode($expectedRedirect));
    }

    private function fakeSuccessfulGoogleCallback(string $email, string $id): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'temporary-token']),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'sub' => $id,
                'email' => $email,
                'name' => 'Google Test User',
            ]),
        ]);

        $this->withSession(['auth.google_oauth_state' => 'valid-state']);
    }
}
