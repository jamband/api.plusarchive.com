<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use App\Scopes\SortableScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 *
 * @mixin Builder<self>
 */
class StoreTag extends Model
{
    use StoreTagScope;
    use SortableScope;

    public $timestamps = false;

    /**
     * @return array<string>
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
     * @return array<int>
     */
    public function getIdsByNames(array $names): array
    {
        $ids = [];

        foreach ($names as $name) {
            $id = $this->ofName($name)->value($this->getKeyName());

            if (null === $id) {
                $tag = new self();
                $tag->name = $name;
                $tag->save();
                $ids[] = $tag->id;

                continue;
            }

            $ids[] = $id;
        }

        return $ids;
    }
}
