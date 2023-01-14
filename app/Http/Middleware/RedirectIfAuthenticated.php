<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;

class RedirectIfAuthenticated
{
    public function __construct(
        private readonly AuthManager $auth,
        private readonly ResponseFactory $response,
    ) {
    }

    public function handle(
        Request $request,
        Closure $next,
        mixed ...$guards
    ): mixed {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->response->json(
                    data: ['message' => 'Bad Request.'],
                    status: 400,
                );
            }
        }

        return $next($request);
    }
}
