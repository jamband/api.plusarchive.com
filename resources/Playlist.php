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
