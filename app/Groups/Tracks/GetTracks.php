<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetTracks extends Controller
{
    public function __construct(
        private readonly Track $track,
        private readonly Request $request,
    ) {
    }

    public function __invoke(): TrackResourceCollection
    {
        /** @var Track $query */
        $query = $this->track::query()
            ->with('provider')
            ->with('genres');

        $provider = $this->request->query('provider');
        if (is_string($provider)) {
            $query->ofProvider($provider);
        }

        $genre = $this->request->query('genre');
        if (is_string($genre)) {
            $query->ofGenre($genre);
        }

        return new TrackResourceCollection(
            $query->latest()->paginate(24)
        );
    }
}
