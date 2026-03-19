<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class DeleteTrack
{
    public function __construct(
        private Track $track,
        private Hashids $hashids,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(string $hash): Response
    {
        $id = $this->hashids->decode($hash);
        $id = empty($id) ? 0 : $id[0];

        $this->track->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
