<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use App\Groups\BookmarkTags\BookmarkTag;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetBookmarkTags extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
        private readonly BookmarkTag $tag,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->make(
            $this->tag->getNames(),
        );
    }
}
