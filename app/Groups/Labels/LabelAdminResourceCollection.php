<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use App\Http\Resources\Json\ResourceCollection;

class LabelAdminResourceCollection extends ResourceCollection
{
    public $collects = LabelAdminResource::class;
}
