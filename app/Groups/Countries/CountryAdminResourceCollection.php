<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use App\Http\Resources\Json\ResourceCollection;

class CountryAdminResourceCollection extends ResourceCollection
{
    public $collects = CountryAdminResource::class;
}
