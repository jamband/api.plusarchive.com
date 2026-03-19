<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('auth')]
readonly class GetUser
{
    public function __construct(
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make([
            'role' => 'admin',
        ]);
    }
}
