<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Hashids\Hashids;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdatePlaylist extends Controller
{
    public function __construct(
        private readonly Playlist $playlist,
        private readonly Hashids $hashids,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
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
