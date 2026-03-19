<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateStoreTag
{
    public function __construct(
        private StoreTag $tag,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateStoreTagRequest $request, int $id): Response
    {
        $tag = $this->tag->findOrFail($id);
        $request->save($tag);

        return $this->response->make(
            $tag->toResource(StoreTagAdminResource::class),
        );
    }
}
