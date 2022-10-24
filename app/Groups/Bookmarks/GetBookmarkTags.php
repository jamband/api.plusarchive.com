<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use App\Groups\BookmarkTags\BookmarkTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetBookmarkTags extends Controller
{
    public function __construct(
        private ResponseFactory $response,
        private BookmarkTag $tag,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return $this->response->json(
            data: $this->tag->getNames(),
        );
    }
}
