<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class UpdateBookmarkTag extends Controller
{
    public function __construct(
        private readonly BookmarkTag $tag,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
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
