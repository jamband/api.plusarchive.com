<?php

declare(strict_types=1);

namespace Tests\Feature\Exceptions;

use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Tests\TestCase;

class HandlerTest extends TestCase
{
    private Router $router;
    private string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app->make(Router::class);
        $this->uri = '/testing-'.mb_strtolower(Str::random(10));
    }

    public function testNotFound(): void
    {
        $this->router->middleware('web')
            ->get($this->uri, fn () => $this->app->abort(404));

        $this->get($this->uri)
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testMethodNotAllowed(): void
    {
        $this->router->middleware('web')
            ->post($this->uri, fn () => null);

        $this->get($this->uri)
            ->assertStatus(405)
            ->assertExactJson(['message' => 'Method Not Allowed.']);
    }
}
