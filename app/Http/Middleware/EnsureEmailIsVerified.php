<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;

class EnsureEmailIsVerified
{
    public function __construct(
        private ResponseFactory $response,
    ) {
    }

    public function handle(Request $request, Closure $next): JsonResponse|null
    {
        if (
            $request->user() instanceof MustVerifyEmail &&
            !$request->user()->hasVerifiedEmail()
        ) {
            return $this->response->json(
                data: ['message' => 'Your email address is not verified.'],
                status: 409,
            );
        }

        return $next($request);
    }
}
