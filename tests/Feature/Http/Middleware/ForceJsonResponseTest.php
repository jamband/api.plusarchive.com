<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Routing\Router;
use Tests\TestCase;

class ForceJsonResponseTest extends TestCase
{
    private Router $router;
    private string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app->make(Router::class);
        $this->uri = uniqid('/testing-');
    }

    protected function tearDown(): void
    {
        $this->artisan('view:clear');

        parent::tearDown();
    }

    public function testWithoutForceJsonResponse(): void
    {
        $this->router->get($this->uri);

        $this->withoutMiddleware(ForceJsonResponse::class)
            ->head($this->uri)
            ->assertHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function testForceJsonResponse(): void
    {
        $this->router->get($this->uri);

        $this->head($this->uri)
            ->assertHeader('Content-Type', 'application/json');
    }
}
