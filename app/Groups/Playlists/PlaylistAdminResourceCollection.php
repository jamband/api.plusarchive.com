<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use App\Http\Resources\Json\ResourceCollection;

class PlaylistAdminResourceCollection extends ResourceCollection
{
    public $collects = PlaylistAdminResource::class;
}
