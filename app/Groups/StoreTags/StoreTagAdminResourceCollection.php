<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(StoreTagAdminResource::class)]
class StoreTagAdminResourceCollection extends ResourceCollection
{
}
