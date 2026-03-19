<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdateBookmarkTag
{
    public function __construct(
        private BookmarkTag $tag,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(UpdateBookmarkTagRequest $request, int $id): Response
    {
        $tag = $this->tag->findOrFail($id);
        $request->save($tag);

        return $this->response->make(
            $tag->toResource(BookmarkTagAdminResource::class),
        );
    }
}
