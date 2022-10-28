<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testGuestMiddleware(): void
    {
        $this->assertGuestMiddleware('POST /auth/login');
    }

    public function testLoginFails(): void
    {
        $user = UserFactory::new()
            ->createOne();

        $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ])
            ->assertUnprocessable()
            ->assertExactJson(['errors' => [
                'email' => __('auth.failed'),
            ]]);

        $this->assertGuest();
    }

    public function testLogin(): void
    {
        $user = UserFactory::new()
            ->createOne();

        $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertNoContent();

        $this->assertAuthenticated();
    }
}
