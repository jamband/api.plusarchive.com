<?php

declare(strict_types=1);

namespace App\Groups\Site;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetCsrfCookie extends Controller
{
    public function __construct(
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            status: 204,
        );
    }
}
