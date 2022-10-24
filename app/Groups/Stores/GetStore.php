<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetStore extends Controller
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        return $this->response->json(
            data: new StoreAdminResource(
                $this->store::query()
                    ->with('country')
                    ->with('tags')
                    ->findOrFail($id)
            ),
        );
    }
}
