<?php

declare(strict_types=1);

namespace app\models;

use app\queries\BookmarkQuery;
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
 * @property BookmarkTag[] $tags
 */
class Bookmark extends ActiveRecord
{
    use ActiveRecordTrait;

    public static function tableName(): string
    {
        return 'bookmark';
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

    public static function find(): BookmarkQuery
    {
        return new BookmarkQuery(static::class);
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTags(): ActiveQuery
    {
        return $this->hasMany(BookmarkTag::class, ['id' => 'bookmark_tag_id'])
            ->viaTable('bookmark_tag_assn', ['bookmark_id' => 'id'])
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
