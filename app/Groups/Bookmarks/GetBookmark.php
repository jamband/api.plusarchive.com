<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetBookmark
{
    public function __construct(
        private Bookmark $bookmark,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        return $this->response->make(
            $this->bookmark::query()
                ->with('country')
                ->with('tags')
                ->findOrFail($id)
                ->toResource(BookmarkAdminResource::class),
        );
    }
}
