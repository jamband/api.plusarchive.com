<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Response;
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

    public function __invoke(CreateBookmarkRequest $request): Response
    {
        $request->save($this->bookmark);

        return $this->response->make(
            $this->bookmark->toResource(BookmarkAdminResource::class),
            201,
        )
            ->header('Location', $this->url->to(
                '/bookmarks/'.$this->bookmark->id
            ));
    }
}
