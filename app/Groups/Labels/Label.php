<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use App\Groups\Countries\Country;
use App\Groups\LabelTags\LabelTag;
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
 * @property-read array<int, LabelTag> $tags
 *
 * @mixin Builder<self>
 */
class Label extends Model
{
    use LabelScope;
    use SortableScope;

    /**
     * @return BelongsTo<Country, self>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsToMany<LabelTag>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            related: LabelTag::class,
            table: 'tag_label',
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
