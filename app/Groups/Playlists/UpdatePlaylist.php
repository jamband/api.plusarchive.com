<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Hashids\Hashids;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdatePlaylist
{
    public function __construct(
        private Playlist $playlist,
        private Hashids $hashids,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdatePlaylistRequest $request, string $hash): Response
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];

        $playlist = $this->playlist->findOrFail($id);
        $request->save($playlist);

        return $this->response->make(
            $playlist->toResource(PlaylistAdminResource::class),
        );
    }
}
