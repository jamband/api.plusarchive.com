<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use App\Http\Resources\Json\ResourceCollection;

class LabelTagAdminResourceCollection extends ResourceCollection
{
    public $collects = LabelTagAdminResource::class;
}
