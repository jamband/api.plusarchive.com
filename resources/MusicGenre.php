<?php

/*
 * This file is part of the api.plusarchive.com
 *
 * (c) Tomoki Morita <tmsongbooks215@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace app\resources;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property int $frequency
 * @property int $created_at
 * @property int $updated_at
 */
class MusicGenre extends ActiveRecord
{
    use ResourceTrait;

    public static function tableName(): string
    {
        return 'music_genre';
    }

    public function fields(): array
    {
        return [
            'name',
        ];
    }

    public static function minimal(int $limit): array
    {
        $data = static::find()
            ->select('name')
            ->orderBy(['frequency' => SORT_DESC])
            ->limit($limit)
            ->column();

        sort($data, SORT_STRING);

        return $data;
    }
}
