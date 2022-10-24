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
}
