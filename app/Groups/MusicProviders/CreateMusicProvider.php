<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateMusicProvider extends Controller
{
    public function __construct(
        private readonly MusicProvider $provider,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
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
