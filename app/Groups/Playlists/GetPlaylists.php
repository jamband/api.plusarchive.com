<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetPlaylists
{
    public function __construct(
        private Playlist $playlist,
        private ResponseFactory $response,
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
