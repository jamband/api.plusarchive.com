<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetStoreTag
{
    public function __construct(
        private StoreTag $tag,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            $this->tag::query()
                ->findOrFail($id),
        );
    }
}
