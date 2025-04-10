<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Store
 */
class StoreResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $tags = [];
        foreach ($this->tags as $tag) {
            $tags[] = $tag->name;
        }

        return [
            'name' => $this->name,
            'country' => $this->country->name,
            'url' => $this->url,
            'links' => $this->links,
            'tags' => $tags,
        ];
    }

    protected static function newCollection($resource): StoreResourceCollection
    {
        return new StoreResourceCollection($resource);
    }
}
