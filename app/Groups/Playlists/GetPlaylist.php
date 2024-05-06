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
        private readonly Hashids $hashids,
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(string $hash): Response
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];
        assert(is_int($id));

        return $this->response->make(
            new PlaylistResource(
                Playlist::query()
                    ->findOrFail($id)
            ),
        );
    }
}
