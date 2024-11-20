<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

readonly class EnsureEmailIsVerified
{
    public function __construct(
        private ResponseFactory $response,
    ) {
    }

    /**
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isMustVerifyEmail = $request->user() instanceof MustVerifyEmail;

        /** @var MustVerifyEmail $user */
        $user = $request->user();

        if (!$request->user() || ($isMustVerifyEmail && !$user->hasVerifiedEmail())) {
            return $this->response->make(
                ['message' => 'Your email address is not verified.'],
                409,
            );
        }

        return $next($request);
    }
}
