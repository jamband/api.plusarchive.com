<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateStore extends Controller
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        CreateStoreRequest $request,
    ): JsonResponse {
        $request->save($this->store);

        return $this->response->json(
            data: new StoreAdminResource($this->store),
            status: 201,
        )
            ->header('Location', $this->url->to(
                '/stores/'.$this->store->id
            ));
    }
}
