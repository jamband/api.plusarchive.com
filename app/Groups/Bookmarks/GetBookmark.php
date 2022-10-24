<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetBookmark extends Controller
{
    public function __construct(
        private Bookmark $bookmark,
        private ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        return $this->response->json(
            data: new BookmarkAdminResource(
                $this->bookmark::query()
                    ->with('country')
                    ->with('tags')
                    ->findOrFail($id)
            ),
        );
    }
}
