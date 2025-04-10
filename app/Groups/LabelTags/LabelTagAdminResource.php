<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LabelTag
 */
class LabelTagAdminResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    protected static function newCollection($resource): LabelTagAdminResourceCollection
    {
        return new LabelTagAdminResourceCollection($resource);
    }
}
