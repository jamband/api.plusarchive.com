<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenre;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetTrackGenres extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly TrackGenre $genre,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->genre->getNames(),
        );
    }
}
