<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateStoreTag extends Controller
{
    public function __construct(
        private readonly StoreTag $tag,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        UpdateStoreTagRequest $request,
        int $id,
    ): JsonResponse {
        $tag = $this->tag::query()
            ->findOrFail($id);

        assert($tag instanceof StoreTag);

        $request->save($tag);

        return $this->response->json(
            data: new StoreTagAdminResource($tag),
        );
    }
}
