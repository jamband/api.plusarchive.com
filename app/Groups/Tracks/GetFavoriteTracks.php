<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetFavoriteTracks
{
    public function __construct(
        private Track $track,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        /** @var Track $query */
        $query = $this->track::query()
            ->with('provider')
            ->with('genres');

        return $this->response->make(
            $query->favorites()
                ->latest()
                ->get()
                ->toResourceCollection(TrackResource::class),
        );
    }
}
