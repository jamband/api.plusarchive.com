<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Tests\TestCase;

class ForceJsonResponseTest extends TestCase
{
    private Router $router;
    private string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app->make(Router::class);
        $this->uri = '/testing-'.mb_strtolower(Str::random(10));
    }

    public function testWithoutForceJsonResponseMiddleware(): void
    {
        $this->router->middleware('web')
            ->get($this->uri, fn () => $this->app->abort(400));

        $this->withoutMiddleware(ForceJsonResponse::class)
            ->head($this->uri)
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    public function testWithWebMiddleware(): void
    {
        $this->router->middleware('web')
            ->get($this->uri, fn () => $this->app->abort(400, 'foo'));

        $this->get($this->uri)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson(['message' => 'foo']);
    }
}
