<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method self favorites()
 * @see self::scopeFavorites()
 *
 * @method self ofProvider(string $provider)
 * @see self::scopeOfProvider()
 *
 * @method self ofUrge(string $urge)
 * @see self::scopeOfUrge()
 *
 * @method self ofGenre(string $genre)
 * @see self::scopeOfGenre()
 *
 * @method self ofSearch(string $search)
 * @see self::scopeOfSearch()
 *
 * @method self inTitleOrder()
 * @see self::scopeInTitleOrder()
 */
trait TrackScope
{
    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeFavorites(Builder $query): Builder
    {
        return $query->where('urge', true);
    }

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
     * @param string $urge
     * @return Builder<self>
     */
    public function scopeOfUrge(Builder $query, string $urge): Builder
    {
        return $query->where('urge', (bool)$urge);
    }

    /**
     * @param Builder<self> $query
     * @param string $genre
     * @return Builder<self>
     */
    public function scopeOfGenre(Builder $query, string $genre): Builder
    {
        return $query->whereHas(
            $this->genres()->getRelationName(),
            fn (Builder $query) => $query->where('name', $genre)
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

    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeInTitleOrder(Builder $query): Builder
    {
        return $query->orderBy('title');
    }
}
