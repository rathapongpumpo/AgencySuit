<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        if (! app()->environment('testing')) {
            $exceptions->render(function (AuthorizationException $e, Request $request) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthorized'], 403);
                }

                return redirect()->route('today')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ หรือไม่พบข้อมูลดังกล่าว');
            });

            $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthorized'], 403);
                }

                return redirect()->route('today')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้ หรือไม่พบข้อมูลดังกล่าว');
            });
        }
    })->create();
