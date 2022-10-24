<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use App\Http\Resources\Json\ResourceCollection;

class TrackGenreAdminResourceCollection extends ResourceCollection
{
    public $collects = TrackGenreAdminResource::class;
}
