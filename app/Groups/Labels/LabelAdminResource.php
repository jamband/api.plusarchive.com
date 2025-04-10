<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Label
 */
class LabelAdminResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country->name,
            'url' => $this->url,
            'links' => $this->links,
            'tags' => $tags,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
        ];
    }

    protected static function newCollection($resource): LabelAdminResourceCollection
    {
        return new LabelAdminResourceCollection($resource);
    }
}
