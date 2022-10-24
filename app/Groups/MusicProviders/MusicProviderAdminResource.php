<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MusicProvider
 */
class MusicProviderAdminResource extends JsonResource
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
