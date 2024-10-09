<?php

declare(strict_types=1);

namespace Tests\Feature\Exceptions;

use App\Groups\Users\UserFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Tests\TestCase;

class ExceptionsTest extends TestCase
{
    private Router $router;
    private string $uri;
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app->make(Router::class);
        $this->uri = uniqid('/testing-');
        $this->userFactory = new UserFactory();
    }

    public function testUnauthorized(): void
    {
        $this->router->middleware('auth')
            ->get($this->uri);

        $this->get($this->uri)
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }


    public function testNotFound(): void
    {
        $this->get($this->uri)
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testMethodNotAllowed(): void
    {
        $this->router->post($this->uri);

        $this->get($this->uri)
            ->assertMethodNotAllowed()
            ->assertExactJson(['message' => 'Method Not Allowed.']);
    }

    public function testHttpException(): void
    {
        $this->router->get($this->uri, function () {
            $this->app->abort(400, 'foo');
        });

        $this->get($this->uri)
            ->assertBadRequest()
            ->assertExactJson(['message' => 'foo']);
    }

    public function testVerifiedEmail(): void
    {
        $this->router->middleware('verified')
            ->get($this->uri);

        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get($this->uri)
            ->assertConflict()
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    public function testValidation(): void
    {
        $this->router->post($this->uri, function (Request $request) {
            $request->validate(['foo' => 'required']);
        });

        $this->post($this->uri)
            ->assertUnprocessable()
            ->assertExactJson(['errors.foo' => __('validation.required', [
                'attribute' => 'foo',
            ])]);
    }
}
