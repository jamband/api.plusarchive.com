<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use App\Http\Resources\Json\ResourceCollection;

class StoreAdminResourceCollection extends ResourceCollection
{
    public $collects = StoreAdminResource::class;
}
