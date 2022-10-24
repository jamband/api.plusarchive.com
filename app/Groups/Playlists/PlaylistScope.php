<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method self ofProvider(string $provider)
 * @see self::scopeOfProvider()
 *
 * @method self ofSearch(string $search)
 * @see self::scopeOfSearch()
 */
trait PlaylistScope
{
    /**
     * @param Builder<self> $query
     * @param string $provider
     * @return Builder<self>
     */
    public function scopeOfProvider(Builder $query, string $provider): Builder
    {
        return $query->whereHas(
            $this->provider()->getRelationName(),
            fn (Builder $query) => $query->where('name', $provider)
        );
    }

    /**
     * @param Builder<self> $query
     * @param string $search
     * @return Builder<self>
     */
    public function scopeOfSearch(Builder $query, string $search): Builder
    {
        return '' === $search
            ? $query->where('title', '')
            : $query->where('title', 'like', '%'.addcslashes($search, '\\%_').'%');
    }
}
