<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use App\Http\Resources\Json\ResourceCollection;

class StoreResourceCollection extends ResourceCollection
{
    public $collects = StoreResource::class;
}
