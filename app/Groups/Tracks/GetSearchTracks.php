<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

readonly class GetSearchTracks
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

        $search = $this->request->query('q');
        $search = is_string($search) ? $search : '';

        return $query->ofSearch($search)
            ->inTitleOrder()
            ->paginate(24)
            ->toResourceCollection(TrackResource::class);
    }
}
