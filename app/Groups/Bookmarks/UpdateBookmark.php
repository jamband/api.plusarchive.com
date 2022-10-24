<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateBookmark extends Controller
{
    public function __construct(
        private Bookmark $bookmark,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        UpdateBookmarkRequest $request,
        int $id,
    ): JsonResponse {
        $bookmark = $this->bookmark->findOrFail($id);
        assert($bookmark instanceof Bookmark);

        $request->save($bookmark);

        return $this->response->json(
            data: new BookmarkAdminResource($bookmark),
        );
    }
}
