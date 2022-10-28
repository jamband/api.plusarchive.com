<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Hashids\Hashids;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeletePlaylist extends Controller
{
    public function __construct(
        private Playlist $playlist,
        private Hashids $hashids,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(string $hash): JsonResponse
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];
        assert(is_int($id));

        $this->playlist::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->json(
            status: 204,
        );
    }
}