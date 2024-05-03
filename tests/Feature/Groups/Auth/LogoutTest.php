<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/auth/logout')
            ->assertUnauthorized();
    }

    public function testLogout(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->post('/auth/logout')
            ->assertNoContent();

        $this->assertGuest();
    }
}
