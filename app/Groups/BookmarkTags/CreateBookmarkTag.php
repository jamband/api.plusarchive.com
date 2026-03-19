<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateBookmarkTag
{
    public function __construct(
        private BookmarkTag $tag,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateBookmarkTagRequest $request): Response
    {
        $request->save($this->tag);

        return $this->response->make(
            $this->tag->toResource(BookmarkTagAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/bookmark-tags/'.$this->tag->id
            ));
    }
}
