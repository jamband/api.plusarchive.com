<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use App\Groups\BookmarkTags\BookmarkTag;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

readonly class GetBookmarkTags
{
    public function __construct(
        private ResponseFactory $response,
        private BookmarkTag $tag,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->tag->getNames(),
        );
    }
}
