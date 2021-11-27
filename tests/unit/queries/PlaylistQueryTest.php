<?php

declare(strict_types=1);

namespace app\tests\unit\queries;

use app\models\Music;
use app\models\Playlist;
use app\tests\Database;
use app\tests\TestCase;

class PlaylistQueryTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('music');

        parent::setUp();
    }

    public function testInit(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_YOUTUBE, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time(), time()],
            ['url2', Music::PROVIDER_YOUTUBE, 'key2', 'title2', 'image2', Music::TYPE_PLAYLIST, false, time(), time()],
            ['url3', Music::PROVIDER_YOUTUBE, 'key3', 'title3', 'image3', Music::TYPE_PLAYLIST, false, time(), time()],
        ]);

        $data = Playlist::find()->all();
        $this->assertSame(2, count($data));
        $this->assertSame('url2', $data[0]->url);
        $this->assertSame('url3', $data[1]->url);
    }
}
