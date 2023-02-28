<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use App\Scopes\SortableScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 *
 * @mixin Builder<self>
 */
class MusicProvider extends Model
{
    use MusicProviderScope;
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
        return static::query()
            ->select('name')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }
}
