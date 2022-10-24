<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method self ofName(string $name)
 * @see self::scopeOfName()
 *
 * @method self ofSearch(string $search)
 * @see self::scopeOfSearch()
 */
trait StoreTagScope
{
    /**
     * @param Builder<self> $query
     * @param string $name
     * @return Builder<self>
     */
    public function scopeOfName(Builder $query, string $name): Builder
    {
        return $query->where('name', $name);
    }

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
}
