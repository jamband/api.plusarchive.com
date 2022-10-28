<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetTrack extends Controller
{
    public function __construct(
        private Track $track,
        private Hashids $hashids,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(string $hash): JsonResponse
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];
        assert(is_int($id));

        return $this->response->json(
            data: new TrackResource(
                $this->track::query()
                    ->with('genres')
                    ->findOrFail($id)
            ),
        );
    }
}