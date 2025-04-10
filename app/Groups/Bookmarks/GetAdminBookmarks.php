<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class GetAdminBookmarks extends Controller
{
    public function __construct(
        private readonly Bookmark $bookmark,
        private readonly Request $request,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Bookmark $query */
        $query = $this->bookmark::query()
            ->with('country')
            ->with('tags');

        $name = $this->request->query('name');
        if (is_string($name)) {
            $query->ofSearch($name);
        }

        $country = $this->request->query('country');
        if (is_string($country)) {
            $query->ofCountry($country);
        }

        $tag = $this->request->query('tag');
        if (is_string($tag)) {
            $query->ofTag($tag);
        }

        $sort = $this->request->query('sort');

        $sortableColumns = [
            'name',
            $this->bookmark->getCreatedAtColumn(),
            $this->bookmark->getUpdatedAtColumn(),
        ];

        if (
            is_string($sort) &&
            in_array(trim($sort, '-'), $sortableColumns, true)
        ) {
            $query->sort($sort);
        } else {
            $query->latest();
        }

        return $query->paginate(24)
            ->toResourceCollection(BookmarkAdminResource::class);
    }
}
