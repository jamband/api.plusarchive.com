<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class Login extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('guest');
    }

    public function __invoke(
        LoginRequest $request,
    ): JsonResponse {
        $request->authenticate();
        $request->session()->regenerate();

        return $this->response->json(
            status: 204,
        );
    }
}
