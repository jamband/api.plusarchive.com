<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory;

class GetFavoriteTracks
{
    public function __construct(
        private Track $track,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        /** @var Track $query */
        $query = $this->track::query()
            ->with('provider')
            ->with('genres');

        return $this->response->json(
            data: TrackResource::collection(
                $query->favorites()
                    ->latest()
                    ->get()
            ),
        );
    }
}
