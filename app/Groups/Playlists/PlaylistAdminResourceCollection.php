<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(PlaylistAdminResource::class)]
class PlaylistAdminResourceCollection extends ResourceCollection
{
}
