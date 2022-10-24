<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Country
 */
class CountryAdminResource extends JsonResource
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
