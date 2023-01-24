<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class StopUrges extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly Track $track,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): JsonResponse
    {
        $this->track->stopUrges();

        return $this->response->json(
            status: 204,
        );
    }
}
