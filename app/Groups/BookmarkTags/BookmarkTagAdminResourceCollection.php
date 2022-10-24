<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use App\Http\Resources\Json\ResourceCollection;

class BookmarkTagAdminResourceCollection extends ResourceCollection
{
    public $collects = BookmarkTagAdminResource::class;
}
