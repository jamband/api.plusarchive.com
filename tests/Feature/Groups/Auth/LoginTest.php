<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
    }

    public function testGuestMiddleware(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->post('/auth/login')
            ->assertBadRequest();
    }

    public function testLoginFails(): void
    {
        $this->post('/auth/login', [
            'email' => 'foo@example.com',
            'password' => 'wrong_password',
        ])
            ->assertUnprocessable()
            ->assertExactJson(['errors.email' => __('auth.failed')]);

        $this->assertGuest();
    }

    public function testLogin(): void
    {
        $user = $this->userFactory
            ->createOne();

        $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertNoContent();

        $this->assertAuthenticated();
    }
}
