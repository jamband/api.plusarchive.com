<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\MusicProviders\MusicProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetTrackProviders extends Controller
{
    public function __construct(
        private ResponseFactory $response,
        private MusicProvider $provider,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: $this->provider->getNames(),
        );
    }
}
