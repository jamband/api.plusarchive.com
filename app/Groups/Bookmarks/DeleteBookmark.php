<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteBookmark extends Controller
{
    public function __construct(
        private readonly Bookmark $bookmark,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(int $id): Response
    {
        $this->bookmark::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
