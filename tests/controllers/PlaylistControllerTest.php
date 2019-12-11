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

namespace app\tests\controllers;

use app\resources\Playlist;
use app\tests\Database;
use app\tests\RequestHelper;
use app\tests\TestCase;
use Yii;
use yii\web\NotFoundHttpException;

class PlaylistControllerTest extends TestCase
{
    use RequestHelper;

    protected function setUp(): void
    {
        Database::createTable('music');
    }

    public function testActionIndex(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Playlist::PROVIDER_SOUNDCLOUD, 'key1', 'title1', 'image1', Playlist::TYPE_PLAYLIST, false, time() + 2, time()],
            ['url2', Playlist::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Playlist::TYPE_PLAYLIST, false, time() + 3, time()],
            ['url3', Playlist::PROVIDER_YOUTUBE, 'key3', 'title3', 'image3', Playlist::TYPE_TRACK, false, time() + 1, time()],
        ]);

        $data = $this->request('playlists');
        $this->assertSame(2, count($data['items']));
        $this->assertSame('url2', $data['items'][0]['url']);
        $this->assertSame('url1', $data['items'][1]['url']);
        $this->assertArrayNotHasKey('_meta', $data);
    }

    public function testActionView(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Playlist::PROVIDER_SOUNDCLOUD, 'key1', 'title1', 'image1', Playlist::TYPE_PLAYLIST, false, time(), time()],
        ]);

        $data = $this->request('playlists/'.Yii::$app->hashids->encode(1));
        $this->assertSame('url1', $data['url']);
    }

    public function testActionViewNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->request('playlists/'.Yii::$app->hashids->encode(1));
    }
}
