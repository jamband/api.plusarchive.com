<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class DeleteBookmarkTag
{
    public function __construct(
        private BookmarkTag $tag,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        $this->tag::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
