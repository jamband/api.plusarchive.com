<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Bookmark
 */
class BookmarkResource extends JsonResource
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
}
