<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateStore
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateStoreRequest $request, int $id): Response
    {
        $store = $this->store->findOrFail($id);
        $request->save($store);

        return $this->response->make(
            $store->toResource(StoreAdminResource::class),
        );
    }
}
