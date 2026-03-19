<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class DeleteStore
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        $this->store::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
