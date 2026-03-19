<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateLabelTag
{
    public function __construct(
        private LabelTag $tag,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateLabelTagRequest $request): Response
    {
        $request->save($this->tag);

        return $this->response->make(
            $this->tag->toResource(LabelTagAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/label-tags/'.$this->tag->id
            ));
    }
}
