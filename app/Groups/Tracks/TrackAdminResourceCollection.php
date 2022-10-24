<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Http\Resources\Json\ResourceCollection;

class TrackAdminResourceCollection extends ResourceCollection
{
    public $collects = TrackAdminResource::class;
}
