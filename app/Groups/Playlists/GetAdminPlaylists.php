<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetAdminPlaylists extends Controller
{
    public function __construct(
        private readonly Playlist $playlist,
        private readonly Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): PlaylistAdminResourceCollection
    {
        /** @var Playlist $query */
        $query = $this->playlist::query()
            ->with('provider');

        $title = $this->request->query('title');
        if (is_string($title)) {
            $query->ofSearch($title);
        }

        $provider = $this->request->query('provider');
        if (is_string($provider)) {
            $query->ofProvider($provider);
        }

        $sort = $this->request->query('sort');

        $sortableColumns = [
            'title',
            'provider',
            $this->playlist->getCreatedAtColumn(),
            $this->playlist->getUpdatedAtColumn(),
        ];

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), $sortableColumns, true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest();
        }

        return new PlaylistAdminResourceCollection(
            $query->paginate(24)
        );
    }
}
