<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreatePlaylist
{
    public function __construct(
        private Playlist $playlist,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreatePlaylistRequest $request): Response
    {
        $request->save($this->playlist);

        return $this->response->make(
            $this->playlist->toResource(PlaylistAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/playlists/'.$this->playlist->id
            ));
    }
}
