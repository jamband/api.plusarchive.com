<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class DeleteBookmark
{
    public function __construct(
        private Bookmark $bookmark,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        $this->bookmark::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
