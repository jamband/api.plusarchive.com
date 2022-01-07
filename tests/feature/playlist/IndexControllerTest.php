<?php

declare(strict_types=1);

namespace app\tests\feature\playlist;

use app\controllers\playlist\IndexController;
use app\models\Music;
use app\tests\Database;
use app\tests\feature\TestCase;

/** @see IndexController */
class IndexControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(Music::tableName());
    }

    public function test(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_SOUNDCLOUD, 'key1', 'title1', 'image1', Music::TYPE_PLAYLIST, false, time() + 2, time()],
            ['url2', Music::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Music::TYPE_PLAYLIST, false, time() + 3, time()],
            ['url3', Music::PROVIDER_YOUTUBE, 'key3', 'title3', 'image3', Music::TYPE_TRACK, false, time() + 1, time()],
        ]);

        $data = $this->endpoint('GET /playlists');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(2, count($data['items']));
        $this->assertSame('url2', $data['items'][0]['url']);
        $this->assertSame('url1', $data['items'][1]['url']);
        $this->assertArrayNotHasKey('_meta', $data);
    }
}
