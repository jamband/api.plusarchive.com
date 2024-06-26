<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteBookmarkTag extends Controller
{
    public function __construct(
        private readonly BookmarkTag $tag,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): Response
    {
        $this->tag::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
