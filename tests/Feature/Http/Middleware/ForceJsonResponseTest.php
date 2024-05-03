<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Tests\TestCase;

class ForceJsonResponseTest extends TestCase
{
    private RouteRegistrar $router;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $this->router = $router->middleware('web');
    }

    public function testWithoutForceJsonResponse(): void
    {
        $this->router->get('/', fn () => $this->app->abort(400));

        $this->withoutMiddleware(ForceJsonResponse::class)
            ->head('/')
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    public function testForceJsonResponse(): void
    {
        $this->router->get('/', fn () => $this->app->abort(400));

        $this->head('/')
            ->assertHeader('Content-Type', 'application/json');
    }
}
