<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateTrack extends Controller
{
    public function __construct(
        private Track $track,
        private Hashids $hashids,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        UpdateTrackRequest $request,
        string $hash,
    ): JsonResponse {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];
        assert(is_int($id));

        $track = $this->track::query()
            ->findOrFail($id);

        assert($track instanceof Track);
        $request->save($track);

        return $this->response->json(
            data: new TrackAdminResource($track),
        );
    }
}
