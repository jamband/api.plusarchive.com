<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateMusicProvider extends Controller
{
    public function __construct(
        private MusicProvider $provider,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        CreateMusicProviderRequest $request,
    ): JsonResponse {
        $request->save($this->provider);

        return $this->response->json(
            data: new MusicProviderAdminResource($this->provider),
            status: 201,
        )
            ->header('Location', $this->url->to(
                '/music-providers/'.$this->provider->id
            ));
    }
}
