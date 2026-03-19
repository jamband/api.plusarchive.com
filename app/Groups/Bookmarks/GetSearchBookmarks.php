<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

readonly class GetSearchBookmarks
{
    public function __construct(
        private Request $request,
        private Bookmark $bookmark,
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
