<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method self ofCountry(string $country)
 * @see self::scopeOfCountry()
 *
 * @method self ofTag(string $tag)
 * @see self::scopeOfTag()
 *
 * @method self ofSearch(string $search)
 * @see self::scopeOfSearch()
 *
 * @method self inNameOrder()
 * @see self::scopeInNameOrder()
 */
trait BookmarkScope
{
    /**
     * @param Builder<self> $query
     * @param string $country
     * @return Builder<self>
     */
    public function scopeOfCountry(Builder $query, string $country): Builder
    {
        return $query->whereHas(
            $this->country()->getRelationName(),
            fn (Builder $query) => $query->where('name', $country)
        );
    }

    /**
     * @param Builder<self> $query
     * @param string $tag
     * @return Builder<self>
     */
    public function scopeOfTag(Builder $query, string $tag): Builder
    {
        return $query->whereHas(
            $this->tags()->getRelationName(),
            fn (Builder $query) => $query->where('name', $tag)
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
            ? $query->where('name', '')
            : $query->where('name', 'like', '%'.addcslashes($search, '\\%_').'%');
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
