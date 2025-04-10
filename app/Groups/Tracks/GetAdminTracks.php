<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class GetAdminTracks extends Controller
{
    public function __construct(
        private readonly Track $track,
        private readonly Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Track $query */
        $query = $this->track::query()
            ->with('provider')
            ->with('genres');

        $provider = $this->request->query('provider');
        if (is_string($provider)) {
            $query->ofProvider($provider);
        }

        $title = $this->request->query('title');
        if (is_string($title)) {
            $query->ofSearch($title);
        }

        $urge = $this->request->query('urge');
        if (is_string($urge)) {
            $query->ofUrge($urge);
        }

        $genre = $this->request->query('genre');
        if (is_string($genre)) {
            $query->ofGenre($genre);
        }

        $sort = $this->request->query('sort');

        $sortableColumns = [
            'title',
            $this->track->getCreatedAtColumn(),
            $this->track->getUpdatedAtColumn(),
        ];

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), $sortableColumns, true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest();
        }

        return $query->paginate(24)
            ->toResourceCollection(TrackAdminResource::class);
    }
}
