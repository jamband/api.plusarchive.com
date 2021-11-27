<?php

declare(strict_types=1);

namespace app\models;

use app\queries\PlaylistQuery;
use Yii;

class Playlist extends Music
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
        ];
    }

    public static function find(): PlaylistQuery
    {
        return new PlaylistQuery(static::class);
    }
}
