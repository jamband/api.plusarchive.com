<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetUserTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /auth/user');
    }

    public function testGetUser(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/auth/user')
            ->assertOk()
            ->assertExactJson(['role' => 'admin']);
    }
}
