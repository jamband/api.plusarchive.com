<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetTrackGenre extends Controller
{
    public function __construct(
        private TrackGenre $genre,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        return $this->response->json(
            data: new TrackGenreAdminResource(
                $this->genre::query()
                    ->findOrFail($id)
            ),
        );
    }
}
