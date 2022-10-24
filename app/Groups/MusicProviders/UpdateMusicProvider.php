<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateMusicProvider extends Controller
{
    public function __construct(
        private MusicProvider $provider,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        UpdateMusicProviderRequest $request,
        int $id,
    ): JsonResponse {
        $provider = $this->provider::query()
            ->findOrFail($id);

        assert($provider instanceof MusicProvider);

        $request->save($provider);

        return $this->response->json(
            data: new MusicProviderAdminResource($provider),
        );
    }
}
