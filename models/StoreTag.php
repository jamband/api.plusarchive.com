<?php

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
