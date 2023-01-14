<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteMusicProvider extends Controller
{
    public function __construct(
        private readonly MusicProvider $provider,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        $this->provider::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->json(
            status: 204,
        );
    }
}
