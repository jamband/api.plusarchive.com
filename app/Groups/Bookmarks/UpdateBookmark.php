<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateBookmark
{
    public function __construct(
        private Bookmark $bookmark,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateBookmarkRequest $request, int $id): Response
    {
        $bookmark = $this->bookmark->findOrFail($id);
        $request->save($bookmark);

        return $this->response->make(
            $bookmark->toResource(BookmarkAdminResource::class),
        );
    }
}
