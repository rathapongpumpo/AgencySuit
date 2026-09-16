<?php

namespace App\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleOAuthService
{
    private const STATE_SESSION_KEY = 'auth.google_oauth_state';

    public function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }

    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put(self::STATE_SESSION_KEY, $state);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]));
    }

    /** @return array{id: string, email: string, name: string} */
    public function identityFromCallback(Request $request): array
    {
        $state = $request->session()->pull(self::STATE_SESSION_KEY);

        if (! is_string($state) || ! hash_equals($state, (string) $request->input('state')) || ! $request->filled('code')) {
            throw new RuntimeException('Invalid Google OAuth callback.');
        }

        $tokenResponse = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
            'code' => $request->string('code')->toString(),
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
            'grant_type' => 'authorization_code',
        ]);

        $accessToken = $tokenResponse->json('access_token');

        if (! $tokenResponse->successful() || ! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('Google OAuth token exchange failed.');
        }

        $profileResponse = Http::withToken($accessToken)->timeout(10)->get('https://openidconnect.googleapis.com/v1/userinfo');
        $id = $profileResponse->json('sub');
        $email = $profileResponse->json('email');
        $name = $profileResponse->json('name');

        if (! $profileResponse->successful() || ! is_string($id) || ! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Google OAuth profile request failed.');
        }

        return [
            'id' => $id,
            'email' => Str::lower($email),
            'name' => is_string($name) && $name !== '' ? $name : Str::before($email, '@'),
        ];
    }
}
