<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TrackGenre
 */
class TrackGenreAdminResource extends JsonResource
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
}
