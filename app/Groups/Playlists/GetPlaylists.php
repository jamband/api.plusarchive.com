<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetPlaylists extends Controller
{
    public function __construct(
        private readonly Playlist $playlist,
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->playlist::query()
                ->with('provider')
                ->latest()
                ->get()
                ->toResourceCollection(PlaylistResource::class),
        );
    }
}
