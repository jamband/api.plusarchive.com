<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\music;

use app\models\Music;
use app\tests\fixtures\BasePlaylistFixture;

class PlaylistFixture extends BasePlaylistFixture
{
    protected function getData(): array
    {
        return [
            'playlist1' => [
                'url' => 'url1',
                'provider' => Music::PROVIDER_SOUNDCLOUD,
                'provider_key' => 'key1',
                'title' => 'title1',
                'image' => 'image1',
                'type' => Music::TYPE_PLAYLIST,
                'urge' => false,
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];
    }
}
