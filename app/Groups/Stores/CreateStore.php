<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateStore
{
    public function __construct(
        private Store $store,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateStoreRequest $request): Response
    {
        $request->save($this->store);

        return $this->response->make(
            $this->store->toResource(StoreAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/stores/'.$this->store->id
            ));
    }
}
