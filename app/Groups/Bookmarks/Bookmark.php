<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\Countries\Country;
use App\Scopes\SortableScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property int $country_id
 * @property string $url
 * @property string $links
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Country $country
 * @property-read array<int, BookmarkTag> $tags
 *
 * @mixin Builder<self>
 */
class Bookmark extends Model
{
    use BookmarkScope;
    use SortableScope;

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsToMany<BookmarkTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            related: BookmarkTag::class,
            table: 'tag_bookmark',
            relatedPivotKey: 'tag_id',
        );
    }

    /**
     * @return array<int, string>
     */
    public function getCountryNames(): array
    {
        $related = $this->country()->getRelated();
        $relatedTable = $related->getTable();

        return static::query()
            ->select($relatedTable.'.name')
            ->distinct()
            ->join(
                $relatedTable,
                $relatedTable.'.'.$related->getKeyName(),
                '=',
                $this->getTable().'.'.$related->getForeignKey()
            )
            ->orderBy($relatedTable.'.name')
            ->pluck($relatedTable.'.name')
            ->toArray();
    }
}
