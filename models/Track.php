<?php

declare(strict_types=1);

namespace app\models;

use app\queries\TrackQuery;
use creocoder\taggable\TaggableBehavior;
use Yii;
use yii\db\ActiveQuery;

/**
 * @property musicGenre[] $genres
 */
class Track extends Music
{
    public function fields(): array
    {
        return [
            'id' => fn(): string => Yii::$app->hashids->encode($this->id),
            'url',
            'provider' => fn(): string => static::PROVIDERS[$this->provider],
            'provider_key',
            'title',
            'image',
            'created_at' => fn(): string => Yii::$app->formatter->asDate($this->created_at),
        ];
    }

    public static function find(): TrackQuery
    {
        return new TrackQuery(static::class);
    }

    /**
     * @noinspection PhpUnused
     */
    public function getGenres(): ActiveQuery
    {
        return $this->hasMany(MusicGenre::class, ['id' => 'music_genre_id'])
            ->viaTable('music_genre_assn', ['music_id' => 'id'])
            ->orderBy(['name' => SORT_ASC]);
    }

    public function behaviors(): array
    {
        return [
            'taggable' => [
                'class' => TaggableBehavior::class,
                'tagRelation' => 'genres',
            ],
        ];
    }
}
