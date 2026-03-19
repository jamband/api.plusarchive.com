<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(MusicProviderAdminResource::class)]
class MusicProviderAdminResourceCollection extends ResourceCollection
{
}
