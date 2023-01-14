<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetUser extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('auth');
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: ['role' => 'admin'],
        );
    }
}
