<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;

class CreateBookmarkTag extends Controller
{
    public function __construct(
        private readonly BookmarkTag $tag,
        private readonly ResponseFactory $response,
        private readonly UrlGenerator $url,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(CreateBookmarkTagRequest $request): Response
    {
        $request->save($this->tag);

        return $this->response->make(
            new BookmarkTagAdminResource($this->tag),
            201,
        )
            ->header('Location', $this->url->to(
                '/bookmark-tags/'.$this->tag->id
            ));
    }
}
