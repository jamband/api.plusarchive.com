<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetStore
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            $this->store::query()
                ->with('country')
                ->with('tags')
                ->findOrFail($id)
                ->toResource(StoreAdminResource::class),
        );
    }
}
