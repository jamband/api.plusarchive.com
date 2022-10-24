<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateStoreTag extends Controller
{
    public function __construct(
        private StoreTag $tag,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        CreateStoreTagRequest $request
    ): JsonResponse {
        $request->save($this->tag);

        return $this->response->json(
            data: new StoreTagAdminResource($this->tag),
            status: 201,
        )
            ->header('Location', $this->url->to(
                '/store-tags/'.$this->tag->id
            ));
    }
}
