<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateBookmark extends Controller
{
    public function __construct(
        private readonly Bookmark $bookmark,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(
        CreateBookmarkRequest $request,
    ): JsonResponse {
        $request->save($this->bookmark);

        return $this->response->json(
            data: new BookmarkAdminResource($this->bookmark),
            status: 201,
        )
            ->header('Location', $this->url->to(
                '/bookmarks/'.$this->bookmark->id
            ));
    }
}
