<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(StoreAdminResource::class)]
class StoreAdminResourceCollection extends ResourceCollection
{
}
