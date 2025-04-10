<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class GetSearchBookmarks extends Controller
{
    public function __construct(
        private readonly Request $request,
        private readonly Bookmark $bookmark,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Bookmark $query */
        $query = $this->bookmark::query()
            ->with('country')
            ->with('tags');

        $search = $this->request->query('q');
        $search = is_string($search) ? $search : '';

        return $query->ofSearch($search)
            ->inNameOrder()
            ->paginate(14)
            ->toResourceCollection(BookmarkResource::class);
    }
}
