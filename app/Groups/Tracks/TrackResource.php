<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Hashids\Hashids;
use Illuminate\Container\Container;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Track
 */
class TrackResource extends JsonResource
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
            'provider_key' => $this->provider_key,
            'title' => $this->title,
            'image' => $this->image,
            'genres' => $genres,
            'created_at' => $this->created_at->format('Y.m.d'),
        ];
    }
}
