<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateTrack
{
    public function __construct(
        private Track $track,
        private Hashids $hashids,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateTrackRequest $request, string $hash): Response
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];

        $track = $this->track->findOrFail($id);
        $request->save($track);

        return $this->response->make(
            $track->toResource(TrackAdminResource::class),
        );
    }
}
