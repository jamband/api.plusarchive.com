<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Attributes\Collects;

#[Collects(LabelResource::class)]
class LabelResourceCollection extends ResourceCollection
{
}
