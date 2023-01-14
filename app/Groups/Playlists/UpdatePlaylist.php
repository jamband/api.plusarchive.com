<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Hashids\Hashids;
use Illuminate\Http\JsonResponse;
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

    public function __invoke(
        UpdatePlaylistRequest $request,
        string $hash,
    ): JsonResponse {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];
        assert(is_int($id));

        $playlist = $this->playlist::query()
            ->findOrFail($id);

        assert($playlist instanceof Playlist);
        $request->save($playlist);

        return $this->response->json(
            data: new PlaylistAdminResource($playlist),
        );
    }
}
