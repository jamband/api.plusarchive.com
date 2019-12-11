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

use app\queries\StoreQuery;
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
 * @property StoreTag[] $tags
 */
class Store extends ActiveRecord
{
    use ResourceTrait;

    public static function tableName(): string
    {
        return 'store';
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

    public static function find(): StoreQuery
    {
        return new StoreQuery(static::class);
    }

    public function getTags(): ActiveQuery
    {
        return $this->hasMany(StoreTag::class, ['id' => 'store_tag_id'])
            ->viaTable('store_tag_assn', ['store_id' => 'id'])
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
