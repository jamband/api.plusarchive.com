<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteTrackGenre extends Controller
{
    public function __construct(
        private readonly TrackGenre $genre,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        $this->genre::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->json(
            status: 204,
        );
    }
}
