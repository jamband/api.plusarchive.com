<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateTrackGenre
{
    public function __construct(
        private TrackGenre $genre,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateTrackGenreRequest $request): Response
    {
        $request->save($this->genre);

        return $this->response->make(
            $this->genre->toResource(TrackGenreAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/genres/'.$this->genre->id
            ));
    }
}
