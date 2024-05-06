<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class EnsureNotAuthenticated
{
    public function __construct(
        private AuthManager $auth,
        private ResponseFactory $response,
    ) {
    }

    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->response->make(
                    ['message' => 'Bad Request.'],
                    400,
                );
            }
        }

        return $next($request);
    }
}
