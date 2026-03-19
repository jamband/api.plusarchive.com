<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use App\Http\Resources\Json\ResourceCollection;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;

#[UseModel(LabelTagAdminResource::class)]
class LabelTagAdminResourceCollection extends ResourceCollection
{
}
