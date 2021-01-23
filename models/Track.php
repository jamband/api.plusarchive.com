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
            'id' => function (): string {
                return Yii::$app->hashids->encode($this->id);
            },
            'url',
            'provider' => function (): string {
                return static::PROVIDERS[$this->provider];
            },
            'provider_key',
            'title',
            'image',
            'created_at' => function (): string {
                return Yii::$app->formatter->asDate($this->created_at);
            },
        ];
    }

    public static function find(): TrackQuery
    {
        return new TrackQuery(static::class);
    }

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
