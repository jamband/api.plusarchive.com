<?php

declare(strict_types=1);

namespace Tests;

use App\Groups\Users\UserFactory;

trait TestMiddleware
{
    protected function assertVerifiedMiddleware(string $request): void
    {
        [$method, $uri] = explode(' ', $request);

        $this->actingAs(UserFactory::new()->unverified()->makeOne())
            ->json($method, $uri)
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    protected function assertAuthMiddleware(string $request): void
    {
        [$method, $uri] = explode(' ', $request);

        $this->json($method, $uri)
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    protected function assertGuestMiddleware(string $request): void
    {
        [$method, $uri] = explode(' ', $request);

        $this->actingAs(UserFactory::new()->unverified()->makeOne())
            ->json($method, $uri)
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Bad Request.']);

        $this->assertAuthenticated();
    }
}
