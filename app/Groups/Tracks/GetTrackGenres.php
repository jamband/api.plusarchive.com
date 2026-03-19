<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenre;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetTrackGenres
{
    public function __construct(
        private ResponseFactory $response,
        private TrackGenre $genre,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->genre->getNames(),
        );
    }
}
