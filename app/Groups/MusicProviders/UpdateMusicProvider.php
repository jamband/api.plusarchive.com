<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateMusicProvider extends Controller
{
    public function __construct(
        private readonly MusicProvider $provider,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(UpdateMusicProviderRequest $request, int $id): Response
    {
        $provider = $this->provider->findOrFail($id);
        $request->save($provider);

        return $this->response->make(
            new MusicProviderAdminResource($provider),
        );
    }
}
