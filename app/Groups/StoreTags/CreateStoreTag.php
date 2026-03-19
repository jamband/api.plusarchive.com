<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateStoreTag
{
    public function __construct(
        private StoreTag $tag,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateStoreTagRequest $request): Response
    {
        $request->save($this->tag);

        return $this->response->make(
            $this->tag->toResource(StoreTagAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/store-tags/'.$this->tag->id
            ));
    }
}
