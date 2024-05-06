<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\MusicProviders\MusicProvider;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetTrackProviders extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly MusicProvider $provider,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->provider->getNames(),
        );
    }
}
