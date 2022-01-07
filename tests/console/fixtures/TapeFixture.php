<?php

declare(strict_types=1);

namespace app\tests\console\fixtures;

use app\models\Music;
use yii\test\ActiveFixture;

class TapeFixture extends ActiveFixture
{
    public $modelClass = Music::class;

    protected function getData(): array
    {
        return [
            'track1' => [
                'url' => 'url1',
                'provider' => Music::PROVIDER_BANDCAMP,
                'provider_key' => 'key1',
                'title' => 'Foo1 Bar1',
                'image' => 'image1',
                'type' => Music::TYPE_TRACK,
                'urge' => true,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'track2' => [
                'url' => 'url2',
                'provider' => Music::PROVIDER_BANDCAMP,
                'provider_key' => 'key2',
                'title' => 'Foo2 Bar2',
                'image' => 'image2',
                'type' => Music::TYPE_TRACK,
                'urge' => false,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'track3' => [
                'url' => 'url3',
                'provider' => Music::PROVIDER_BANDCAMP,
                'provider_key' => 'key3',
                'title' => 'Foo3 Bar3',
                'image' => 'image3',
                'type' => Music::TYPE_TRACK,
                'urge' => true,
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];
    }
}
