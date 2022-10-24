<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use App\Http\Resources\Json\ResourceCollection;

class StoreTagAdminResourceCollection extends ResourceCollection
{
    public $collects = StoreTagAdminResource::class;
}
