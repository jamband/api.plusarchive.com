<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class StopUrges
{
    public function __construct(
        private ResponseFactory $response,
        private Track $track,
    ) {
    }

    public function __invoke(): Response
    {
        $this->track->stopUrges();

        return $this->response->noContent();
    }
}
