<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(TrackGenreAdminResource::class)]
class TrackGenreAdminResourceCollection extends ResourceCollection
{
}
