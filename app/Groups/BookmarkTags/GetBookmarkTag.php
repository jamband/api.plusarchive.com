<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class GetBookmarkTag extends Controller
{
    public function __construct(
        private readonly BookmarkTag $tag,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): JsonResponse
    {
        return $this->response->json(
            data: $this->tag::query()
                ->findOrFail($id),
        );
    }
}
