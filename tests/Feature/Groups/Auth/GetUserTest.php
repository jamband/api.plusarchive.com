<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUserTest extends TestCase
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
        $this->get('/auth/user')
            ->assertUnauthorized();
    }

    public function testGetUser(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/auth/user')
            ->assertOk()
            ->assertExactJson(['role' => 'admin']);
    }
}
