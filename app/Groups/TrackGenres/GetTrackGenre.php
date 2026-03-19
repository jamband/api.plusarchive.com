<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetTrackGenre
{
    public function __construct(
        private TrackGenre $genre,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            $this->genre::query()
                ->findOrFail($id)
                ->toResource(TrackGenreAdminResource::class),
        );
    }
}
