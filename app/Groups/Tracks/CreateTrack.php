<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateTrack
{
    public function __construct(
        private Track $track,
        private ResponseFactory $response,
        private UrlGenerator $url,
        private Hashids $hashids,
    ) {
    }

    public function __invoke(CreateTrackRequest $request): Response
    {
        $request->save($this->track);

        return $this->response->make(
            $this->track->toResource(TrackAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/tracks/'.$this->hashids->encode($this->track->id)
            ));
    }
}
