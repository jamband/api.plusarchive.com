<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenre;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetTrackGenres extends Controller
{
    public function __construct(
        private ResponseFactory $response,
        private TrackGenre $genre,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: $this->genre->getNames(),
        );
    }
}
