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

namespace app\tests\feature\playlist;

use app\models\Music;
use app\tests\Database;
use app\tests\feature\TestCase;
use Yii;

class IndexControllerTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('music');

        parent::setUp();
    }

    public function test(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_SOUNDCLOUD, 'key1', 'title1', 'image1', Music::TYPE_PLAYLIST, false, time() + 2, time()],
            ['url2', Music::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Music::TYPE_PLAYLIST, false, time() + 3, time()],
            ['url3', Music::PROVIDER_YOUTUBE, 'key3', 'title3', 'image3', Music::TYPE_TRACK, false, time() + 1, time()],
        ]);

        $data = $this->request('GET', '/playlists');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, count($data['items']));
        $this->assertSame('url2', $data['items'][0]['url']);
        $this->assertSame('url1', $data['items'][1]['url']);
        $this->assertArrayNotHasKey('_meta', $data);
    }
}
