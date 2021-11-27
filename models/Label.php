<?php

declare(strict_types=1);

namespace app\models;

use app\queries\LabelQuery;
use creocoder\taggable\TaggableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $country
 * @property string $url
 * @property string $link
 * @property int $created_at
 * @property int $updated_at
 *
 * @property LabelTag[] $tags
 */
class Label extends ActiveRecord
{
    use ActiveRecordTrait;

    public static function tableName(): string
    {
        return 'label';
    }

    public function fields(): array
    {
        return [
            'name',
            'country',
            'url',
            'link',
        ];
    }

    public function extraFields(): array
    {
        return [
            'tags'
        ];
    }

    public static function find(): LabelQuery
    {
        return new LabelQuery(static::class);
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTags(): ActiveQuery
    {
        return $this->hasMany(LabelTag::class, ['id' => 'label_tag_id'])
            ->viaTable('label_tag_assn', ['label_id' => 'id'])
            ->orderBy(['name' => SORT_ASC]);
    }

    public function behaviors(): array
    {
        return [
            'taggable' => [
                'class' => TaggableBehavior::class,
                'tagRelation' => 'tags',
            ],
        ];
    }
}
