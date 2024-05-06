<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetFavoriteTracks extends Controller
{
    public function __construct(
        private readonly Track $track,
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        /** @var Track $query */
        $query = $this->track::query()
            ->with('provider')
            ->with('genres');

        return $this->response->make(
            TrackResource::collection(
                $query->favorites()
                    ->latest()
                    ->get()
            ),
        );
    }
}
