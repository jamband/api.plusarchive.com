<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetMusicProvider extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly MusicProvider $provider,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            $this->provider::query()
                ->findOrFail($id)
                ->toResource(MusicProviderAdminResource::class),
        );
    }
}
