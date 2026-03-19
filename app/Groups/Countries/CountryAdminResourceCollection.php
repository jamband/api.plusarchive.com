<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(CountryAdminResource::class)]
class CountryAdminResourceCollection extends ResourceCollection
{
}
