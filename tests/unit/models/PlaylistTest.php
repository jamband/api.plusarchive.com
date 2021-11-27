<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Music;
use app\models\Playlist;
use app\queries\PlaylistQuery;
use app\tests\Database;
use app\tests\TestCase;

class PlaylistTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('music');

        parent::setUp();
    }

    public function testTableName(): void
    {
        $this->assertSame('music', Playlist::tableName());
    }

    public function testFields(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_SOUNDCLOUD, 'key1', 'title1', 'image1', Music::TYPE_PLAYLIST, false, time(), time()],
        ]);

        $data = Playlist::findOne(1)->toArray();
        $this->assertMatchesRegularExpression('/\A[\w-]{11}\z/', $data['id']);
        $this->assertSame('url1', $data['url']);
        $this->assertSame('SoundCloud', $data['provider']);
        $this->assertSame('key1', $data['provider_key']);
        $this->assertSame('title1', $data['title']);
        $this->assertArrayNotHasKey('image', $data);
        $this->assertArrayNotHasKey('type', $data);
        $this->assertArrayNotHasKey('urge', $data);
        $this->assertArrayNotHasKey('created_at', $data);
        $this->assertArrayNotHasKey('updated_at', $data);
    }

    public function testFind(): void
    {
        $query = Playlist::find();
        $this->assertInstanceOf(PlaylistQuery::class, $query);
    }
}
