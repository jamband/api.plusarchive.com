<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use App\Scopes\SortableScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 *
 * @mixin Builder<self>
 */
class TrackGenre extends Model
{
    use SortableScope;
    use TrackGenreScope;

    public $timestamps = false;

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

    /**
     * @param array<int, string> $names
     * @return array<int, int>
     */
    public function getIdsByNames(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {
            $id = $this->ofName($name)->value($this->getKeyName());

            if (null === $id) {
                $genre = new self();
                $genre->name = $name;
                $genre->save();
                $ids[] = $genre->id;

                continue;
            }

            $ids[] = $id;
        }
        return $ids;
    }
}
