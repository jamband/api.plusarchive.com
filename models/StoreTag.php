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

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property int $frequency
 * @property int $created_at
 * @property int $updated_at
 */
class StoreTag extends ActiveRecord
{
    use ActiveRecordTrait;

    public static function tableName(): string
    {
        return 'store_tag';
    }

    public function fields(): array
    {
        return [
            'name',
        ];
    }
}
