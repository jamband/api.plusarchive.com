<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class CreateBookmark
{
    public function __construct(
        private Bookmark $bookmark,
        private ResponseFactory $response,
        private UrlGenerator $url,
    ) {
    }

    public function __invoke(CreateBookmarkRequest $request): Response
    {
        $request->save($this->bookmark);

        return $this->response->make(
            $this->bookmark->toResource(BookmarkAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/bookmarks/'.$this->bookmark->id
            ));
    }
}
