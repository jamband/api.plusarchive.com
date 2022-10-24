<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetMusicProvider extends Controller
{
    public function __construct(
        private ResponseFactory $response,
        private MusicProvider $provider,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        return $this->response->json(
            data: new MusicProviderAdminResource(
                $this->provider::query()
                    ->findOrFail($id)
            ),
        );
    }
}
