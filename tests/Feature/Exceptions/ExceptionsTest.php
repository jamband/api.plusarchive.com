<?php

declare(strict_types=1);

namespace Tests\Feature\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class ExceptionsTest extends TestCase
{
    private RouteRegistrar $router;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $this->router = $router->middleware('web');
    }

    public function testBadRequestHttpException(): void
    {
        $this->router->get('/', function () {
            throw new BadRequestHttpException('foo');
        });

        $this->get('/')
            ->assertBadRequest()
            ->assertExactJson(['message' => 'foo']);
    }

    public function testNotFoundHttpException(): void
    {
        $this->router->get('/foo', fn () => []);

        $this->get('/bar')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testMethodNotAllowedHttpException(): void
    {
        $this->router->post('/', fn () => []);

        $this->get('/')
            ->assertMethodNotAllowed()
            ->assertExactJson(['message' => 'Method Not Allowed.']);
    }

    public function testValidationException(): void
    {
        $this->router->post('/', function (Request $request) {
            $request->validate(['foo' => 'required']);
        });

        $this->post('/')
            ->assertUnprocessable()
            ->assertExactJson(['errors.foo' => __('validation.required', [
                'attribute' => 'foo',
            ])]);
    }
}
