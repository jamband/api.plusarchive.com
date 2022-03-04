<?php

declare(strict_types=1);

namespace app\tests\console\fixtures;

use app\models\Music;
use app\tests\fixtures\BaseMusicFixture;

class TapeFixture extends BaseMusicFixture
{
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
                'provider' => Music::PROVIDER_SOUNDCLOUD,
                'provider_key' => 'key3',
                'title' => 'Foo3 Bar3',
                'image' => 'image3',
                'type' => Music::TYPE_TRACK,
                'urge' => true,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'track4' => [
                'url' => 'url4',
                'provider' => Music::PROVIDER_SOUNDCLOUD,
                'provider_key' => 'key4',
                'title' => 'Foo4 Bar4',
                'image' => 'image4',
                'type' => Music::TYPE_TRACK,
                'urge' => false,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'track5' => [
                'url' => 'url5',
                'provider' => Music::PROVIDER_VIMEO,
                'provider_key' => 'key5',
                'title' => 'Foo5 Bar5',
                'image' => 'image5',
                'type' => Music::TYPE_TRACK,
                'urge' => true,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'track6' => [
                'url' => 'url6',
                'provider' => Music::PROVIDER_YOUTUBE,
                'provider_key' => 'key6',
                'title' => 'Foo6 Bar6',
                'image' => 'image6',
                'type' => Music::TYPE_TRACK,
                'urge' => true,
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];
    }
}
