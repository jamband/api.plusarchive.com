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

namespace app\tests\controllers\playlist;

use app\resources\Playlist;
use app\tests\Database;
use app\tests\WebTestCase;
use Yii;
use yii\web\NotFoundHttpException;

class ViewControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::createTable('music');

        parent::setUp();
    }

    public function testNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->request('GET', '/playlists/'.Yii::$app->hashids->encode(1));
    }

    public function test(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Playlist::PROVIDER_SOUNDCLOUD, 'key1', 'title1', 'image1', Playlist::TYPE_PLAYLIST, false, time(), time()],
        ]);

        $data = $this->request('GET', '/playlists/'.Yii::$app->hashids->encode(1));
        $this->assertSame('url1', $data['url']);
    }
}
