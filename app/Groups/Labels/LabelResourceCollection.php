<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use App\Http\Resources\Json\ResourceCollection;

class LabelResourceCollection extends ResourceCollection
{
    public $collects = LabelResource::class;
}
