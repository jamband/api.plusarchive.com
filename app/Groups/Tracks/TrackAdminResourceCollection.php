<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(TrackAdminResource::class)]
class TrackAdminResourceCollection extends ResourceCollection
{
}
