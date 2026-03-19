<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class DeleteMusicProvider
{
    public function __construct(
        private MusicProvider $provider,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        $this->provider::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
