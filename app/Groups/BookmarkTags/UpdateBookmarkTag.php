<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateBookmarkTag extends Controller
{
    public function __construct(
        private BookmarkTag $tag,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        UpdateBookmarkTagRequest $request,
        int $id,
    ): JsonResponse {
        $tag = $this->tag::query()
            ->findOrFail($id);

        assert($tag instanceof BookmarkTag);

        $request->save($tag);

        return $this->response->json(
            data: new BookmarkTagAdminResource($tag),
        );
    }
}
