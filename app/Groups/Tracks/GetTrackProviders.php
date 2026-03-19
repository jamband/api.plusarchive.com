<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\MusicProviders\MusicProvider;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetTrackProviders
{
    public function __construct(
        private ResponseFactory $response,
        private MusicProvider $provider,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->provider->getNames(),
        );
    }
}
