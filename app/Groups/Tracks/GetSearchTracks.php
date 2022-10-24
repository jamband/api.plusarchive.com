<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetSearchTracks extends Controller
{
    public function __construct(
        private Track $track,
        private Request $request,
    ) {
    }

    public function __invoke(): TrackResourceCollection
    {
        /** @var Track $query */
        $query = $this->track::query()
            ->with('provider')
            ->with('genres');

        $search = $this->request->query('q');
        $search = is_string($search) ? $search : '';

        return new TrackResourceCollection(
            $query->ofSearch($search)
                ->inTitleOrder()
                ->paginate(24)
        );
    }
}
