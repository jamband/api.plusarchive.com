<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetTrackGenre extends Controller
{
    public function __construct(
        private readonly TrackGenre $genre,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            new TrackGenreAdminResource(
                $this->genre::query()
                    ->findOrFail($id)
            ),
        );
    }
}
