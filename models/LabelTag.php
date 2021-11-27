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
class LabelTag extends ActiveRecord
{
    use ActiveRecordTrait;

    public static function tableName(): string
    {
        return 'label_tag';
    }

    public function fields(): array
    {
        return [
            'name',
        ];
    }
}
