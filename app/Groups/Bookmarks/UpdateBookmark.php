<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateBookmark extends Controller
{
    public function __construct(
        private readonly Bookmark $bookmark,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(UpdateBookmarkRequest $request, int $id): Response
    {
        $bookmark = $this->bookmark->findOrFail($id);
        $request->save($bookmark);

        return $this->response->make(
            new BookmarkAdminResource($bookmark),
        );
    }
}
