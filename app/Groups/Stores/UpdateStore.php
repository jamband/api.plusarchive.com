<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateStore extends Controller
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        UpdateStoreRequest $request,
        int $id,
    ): JsonResponse {
        $store = $this->store->findOrFail($id);
        assert($store instanceof Store);

        $request->save($store);

        return $this->response->json(
            data: new StoreAdminResource($store),
        );
    }
}
