<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use App\Http\Resources\Json\ResourceCollection;

class MusicProviderAdminResourceCollection extends ResourceCollection
{
    public $collects = MusicProviderAdminResource::class;
}
