<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method self ofSearch(string $search)
 * @see self::scopeOfSearch()
 *
 * @method self inNameOrder()
 * @see self::scopeInNameOrder()
 */
trait MusicProviderScope
{
    /**
     * @param Builder<self> $query
     * @param string $search
     * @return Builder<self>
     */
    public function scopeOfSearch(Builder $query, string $search): Builder
    {
        return '' === $search
            ? $query->where('name', '')
            : $query->where('name', 'like', '%'.$search.'%');
    }

    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeInNameOrder(Builder $query): Builder
    {
        return $query->orderBy('name');
    }
}
