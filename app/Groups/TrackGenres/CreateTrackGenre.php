<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateTrackGenre extends Controller
{
    public function __construct(
        private readonly TrackGenre $genre,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(CreateTrackGenreRequest $request): Response
    {
        $request->save($this->genre);

        return $this->response->make(
            new TrackGenreAdminResource($this->genre),
            201,
        )
            ->header('Location', $this->url->to(
                '/genres/'.$this->genre->id
            ));
    }
}
