<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('guest')]
readonly class Login
{
    public function __construct(
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(LoginRequest $request): Response
    {
        $request->authenticate();
        $request->session()->regenerate();

        return $this->response->noContent();
    }
}
