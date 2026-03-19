<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

readonly class GetTracks
{
    public function __construct(
        private Track $track,
        private Request $request,
    ) {
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

        $genre = $this->request->query('genre');
        if (is_string($genre)) {
            $query->ofGenre($genre);
        }

        return $query->latest()
            ->paginate(24)
            ->toResourceCollection(TrackResource::class);
    }
}
