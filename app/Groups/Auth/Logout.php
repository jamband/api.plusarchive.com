<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('auth')]
readonly class Logout
{
    public function __construct(
        private AuthManager $auth,
        private Request $request,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        $this->auth->guard('web')->logout();

        $this->request->session()->invalidate();
        $this->request->session()->regenerateToken();

        return $this->response->noContent();
    }
}
