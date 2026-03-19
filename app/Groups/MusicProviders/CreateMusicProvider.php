<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateMusicProvider
{
    public function __construct(
        private MusicProvider $provider,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateMusicProviderRequest $request): Response
    {
        $request->save($this->provider);

        return $this->response->make(
            $this->provider->toResource(MusicProviderAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/music-providers/'.$this->provider->id
            ));
    }
}
