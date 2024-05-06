<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateTrackGenre extends Controller
{
    public function __construct(
        private readonly TrackGenre $genre,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(UpdateTrackGenreRequest $request, int $id): Response
    {
        $genre = $this->genre::query()
            ->findOrFail($id);

        assert($genre instanceof TrackGenre);

        $request->save($genre);

        return $this->response->make(
            new TrackGenreAdminResource($genre),
        );
    }
}
