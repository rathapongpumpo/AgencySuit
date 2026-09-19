<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_user_is_admin_check(): void
    {
        $admin = new User(['name' => 'Admin User', 'email' => 'admin@example.com', 'is_admin' => true]);
        $regularUser = new User(['name' => 'Agent User', 'email' => 'agent@example.com', 'is_admin' => false]);
        $defaultUser = new User(['name' => 'Default User', 'email' => 'default@example.com']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($regularUser->isAdmin());
        $this->assertFalse($defaultUser->isAdmin());
    }

    public function test_admin_middleware_blocks_guest_or_regular_user(): void
    {
        $middleware = new EnsureUserIsAdmin();

        // 1. Guest (no user)
        $requestWithoutUser = Request::create('/admin/users', 'GET');
        try {
            $middleware->handle($requestWithoutUser, fn () => response('OK'));
            $this->fail('Expected HttpException 403 for guest');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        // 2. Regular user (is_admin = false)
        $regularUser = new User(['name' => 'Regular', 'is_admin' => false]);
        $requestWithRegularUser = Request::create('/admin/users', 'GET');
        $requestWithRegularUser->setUserResolver(fn () => $regularUser);

        try {
            $middleware->handle($requestWithRegularUser, fn () => response('OK'));
            $this->fail('Expected HttpException 403 for regular user');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_admin_middleware_allows_admin_user(): void
    {
        $middleware = new EnsureUserIsAdmin();
        $admin = new User(['name' => 'Super Admin', 'is_admin' => true]);

        $request = Request::create('/admin/users', 'GET');
        $request->setUserResolver(fn () => $admin);

        $response = $middleware->handle($request, fn ($req) => response('Authorized'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Authorized', $response->getContent());
    }

    public function test_admin_auth_controller_shows_login_for_guests(): void
    {
        $controller = new \App\Http\Controllers\Admin\AdminAuthController();
        $response = $controller->showLoginForm();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertSame('admin.auth.login', $response->name());
    }
}
