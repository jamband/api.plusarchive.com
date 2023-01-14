<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateTrack extends Controller
{
    public function __construct(
        private readonly Track $track,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
        private readonly Hashids $hashids,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        CreateTrackRequest $request,
    ): JsonResponse {
        $request->save($this->track);

        return $this->response->json(
            data: new TrackAdminResource($this->track),
            status: 201,
        )
            ->header('Location', $this->url->to(
                '/tracks/'.$this->hashids->encode($this->track->id)
            ));
    }
}
