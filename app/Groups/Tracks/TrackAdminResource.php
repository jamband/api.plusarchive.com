<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Container\Container;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Track
 */
class TrackAdminResource extends JsonResource
{
    private Hashids $hashids;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->hashids = Container::getInstance()->make(Hashids::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $genres = [];
        foreach ($this->genres as $genre) {
            $genres[] = $genre->name;
        }

        return [
            'id' => $this->hashids->encode($this->id),
            'url' => $this->url,
            'provider' => $this->provider->name,
            'title' => $this->title,
            'image' => $this->image,
            'urge' => $this->urge,
            'genres' => $genres,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
        ];
    }

    protected static function newCollection($resource): TrackAdminResourceCollection
    {
        return new TrackAdminResourceCollection($resource);
    }
}
