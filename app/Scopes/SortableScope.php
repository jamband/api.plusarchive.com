<?php

declare(strict_types=1);

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method self sort(string $column)
 * @see self::scopeSort()
 */
trait SortableScope
{
    /**
     * @param Builder<self> $query
     * @param string $column
     * @return Builder<self>
     */
    public function scopeSort(Builder $query, string $column): Builder
    {
        return $query->orderBy(
            trim($column, '-'),
            0 === strncmp($column, '-', 1) ? 'desc' : 'asc'
        );
    }
}
