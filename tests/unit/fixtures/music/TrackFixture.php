<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\music;

use app\models\Music;
use app\tests\fixtures\BaseTrackFixture;

class TrackFixture extends BaseTrackFixture
{
    public $depends = [
        TrackGenreFixture::class,
        TrackGenreAssnFixture::class,
    ];

    protected function getData(): array
    {
        return [
            'track1' => [
                'url' => 'url1',
                'provider' => Music::PROVIDER_BANDCAMP,
                'provider_key' => 'key1',
                'title' => 'title1',
                'image' => 'image1',
                'type' => Music::TYPE_TRACK,
                'urge' => false,
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];
    }
}
