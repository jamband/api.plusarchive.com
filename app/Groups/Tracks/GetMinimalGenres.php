<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetMinimalGenres extends Controller
{
    public function __construct(
        private readonly Track $track,
        private readonly Request $request,
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $limit = $this->request->query('limit');
        $limit = is_string($limit) ? (int)$limit : 10;

        return $this->response->json(
            data: $this->track->getMinimalGenres($limit),
        );
    }
}
