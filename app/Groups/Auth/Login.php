<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class Login extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('guest');
    }

    public function __invoke(LoginRequest $request): Response
    {
        $request->authenticate();
        $request->session()->regenerate();

        return $this->response->noContent();
    }
}
