<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreatePlaylist extends Controller
{
    public function __construct(
        private readonly Playlist $playlist,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(CreatePlaylistRequest $request): Response
    {
        $request->save($this->playlist);

        return $this->response->make(
            new PlaylistAdminResource($this->playlist),
            201,
        )
            ->header('Location', $this->url->to(
                '/playlists/'.$this->playlist->id
            ));
    }
}
