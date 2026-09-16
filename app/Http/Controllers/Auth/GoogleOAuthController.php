<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleOAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class GoogleOAuthController extends Controller
{
    public function redirect(Request $request, GoogleOAuthService $google): RedirectResponse
    {
        if (! $google->isConfigured()) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google Login ยังไม่พร้อมใช้งาน กรุณาเข้าสู่ระบบด้วยอีเมล',
            ]);
        }

        return $google->redirect($request);
    }

    public function callback(Request $request, GoogleOAuthService $google): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('login')->withErrors([
                'google' => 'ไม่สามารถเข้าสู่ระบบด้วย Google ได้ กรุณาลองใหม่หรือใช้อีเมล',
            ]);
        }

        if (! $google->isConfigured()) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google Login ยังไม่พร้อมใช้งาน กรุณาเข้าสู่ระบบด้วยอีเมล',
            ]);
        }

        try {
            $identity = $google->identityFromCallback($request);
            $user = $this->findOrCreateUser($identity);
        } catch (Throwable) {
            return redirect()->route('login')->withErrors([
                'google' => 'ไม่สามารถเข้าสู่ระบบด้วย Google ได้ กรุณาลองใหม่หรือใช้อีเมล',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('today', absolute: false));
    }

    /** @param array{id: string, email: string, name: string} $identity */
    private function findOrCreateUser(array $identity): User
    {
        return DB::transaction(function () use ($identity): User {
            $providerUser = User::query()
                ->where('provider', 'google')
                ->where('provider_id', $identity['id'])
                ->lockForUpdate()
                ->first();

            if ($providerUser !== null) {
                return $providerUser;
            }

            $emailUser = User::query()->where('email', $identity['email'])->lockForUpdate()->first();

            if ($emailUser !== null) {
                if ($emailUser->provider !== null && $emailUser->provider !== 'google') {
                    throw new RuntimeException('Email is already associated with another provider.');
                }

                $emailUser->forceFill([
                    'provider' => 'google',
                    'provider_id' => $identity['id'],
                ])->save();

                return $emailUser;
            }

            return User::create([
                'name' => $identity['name'],
                'email' => $identity['email'],
                'provider' => 'google',
                'provider_id' => $identity['id'],
                'password' => null,
            ]);
        });
    }
}
