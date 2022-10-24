<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Hashids\Hashids;
use Illuminate\Container\Container;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Playlist
 */
class PlaylistResource extends JsonResource
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
        return [
            'id' => $this->hashids->encode($this->id),
            'url' => $this->url,
            'provider' => $this->provider->name,
            'provider_key' => $this->provider_key,
            'title' => $this->title,
        ];
    }
}
