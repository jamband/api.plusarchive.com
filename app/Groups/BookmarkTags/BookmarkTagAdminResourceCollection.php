<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(BookmarkTagAdminResource::class)]
class BookmarkTagAdminResourceCollection extends ResourceCollection
{
}
