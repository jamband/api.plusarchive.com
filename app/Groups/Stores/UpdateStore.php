<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateStore extends Controller
{
    public function __construct(
        private readonly Store $store,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
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
