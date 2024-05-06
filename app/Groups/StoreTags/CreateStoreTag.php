<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateStoreTag extends Controller
{
    public function __construct(
        private readonly StoreTag $tag,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(CreateStoreTagRequest $request): Response
    {
        $request->save($this->tag);

        return $this->response->make(
            new StoreTagAdminResource($this->tag),
            201,
        )
            ->header('Location', $this->url->to(
                '/store-tags/'.$this->tag->id
            ));
    }
}
