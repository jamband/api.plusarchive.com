<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Hashids\Hashids;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetPlaylist extends Controller
{
    public function __construct(
        private readonly Playlist $playlist,
        private readonly Hashids $hashids,
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(string $hash): Response
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];

        return $this->response->make(
            $this->playlist->findOrFail($id)
                ->toResource(PlaylistResource::class),
        );
    }
}
