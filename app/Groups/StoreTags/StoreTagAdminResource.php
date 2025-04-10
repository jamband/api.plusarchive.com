<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StoreTag
 */
class StoreTagAdminResource extends JsonResource
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

    protected static function newCollection($resource): StoreTagAdminResourceCollection
    {
        return new StoreTagAdminResourceCollection($resource);
    }
}
