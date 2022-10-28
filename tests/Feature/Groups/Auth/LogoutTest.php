<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /auth/logout');
    }

    public function testLogout(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/auth/logout')
            ->assertNoContent();

        $this->assertGuest();
    }
}
