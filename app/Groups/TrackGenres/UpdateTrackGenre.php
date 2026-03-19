<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateTrackGenre
{
    public function __construct(
        private TrackGenre $genre,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateTrackGenreRequest $request, int $id): Response
    {
        $genre = $this->genre->findOrFail($id);
        $request->save($genre);

        return $this->response->make(
            $genre->toResource(TrackGenreAdminResource::class),
        );
    }
}
