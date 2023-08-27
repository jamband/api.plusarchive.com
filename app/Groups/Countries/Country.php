<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use App\Scopes\SortableScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 *
 * @mixin Builder<self>
 */
class Country extends Model
{
    use CountryScope;
    use SortableScope;

    public $timestamps = false;

    public function getIdByName(string $name): int|null
    {
        return static::query()
            ->select('id')
            ->where('name', $name)
            ->value('id');
    }

    /**
     * @return array<int, string>
     */
    public function getNames(): array
    {
        /** @var array<int, string> $items */
        $items = static::query()
            ->select('name')
            ->orderBy('id')
            ->pluck('name')
            ->toArray();

        $chunk = array_splice($items, 2);
        sort($chunk, SORT_STRING);

        return [...$items, ...$chunk];

    }
}
