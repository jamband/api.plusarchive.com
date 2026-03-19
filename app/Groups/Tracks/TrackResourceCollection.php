<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(TrackResource::class)]
class TrackResourceCollection extends ResourceCollection
{
}
