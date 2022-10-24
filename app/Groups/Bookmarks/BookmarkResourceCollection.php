<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use App\Http\Resources\Json\ResourceCollection;

class BookmarkResourceCollection extends ResourceCollection
{
    public $collects = BookmarkResource::class;
}
