<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetPlaylists extends Controller
{
    public function __construct(
        private readonly Playlist $playlist,
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: PlaylistResource::collection(
                $this->playlist::query()
                    ->with('provider')
                    ->latest()
                    ->get()
            ),
        );
    }
}
