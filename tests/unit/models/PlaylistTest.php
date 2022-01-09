<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Music;
use app\models\Playlist;
use app\queries\PlaylistQuery;
use app\tests\Database;
use app\tests\unit\fixtures\music\PlaylistFixture;
use PHPUnit\Framework\TestCase;
use yii\test\FixtureTrait;

/** @see Playlist */
class PlaylistTest extends TestCase
{
    use FixtureTrait;

    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(Music::tableName());
    }

    public function fixtures(): array
    {
        return [
            'playlist' => PlaylistFixture::class,
        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('music', Playlist::tableName());
    }

    public function testFields(): void
    {
        /** @var PlaylistFixture $fixture */
        $fixture = $this->getFixture('playlist');
        $fixture->load();
        $playlist1Fixture = $fixture->data['playlist1'];

        $data = Playlist::findOne(1)->toArray();
        $this->assertCount(5, $data);
        $this->assertMatchesRegularExpression('/\A[\w-]{11}\z/', $data['id']);
        $this->assertSame($playlist1Fixture['url'], $data['url']);
        $this->assertSame(Music::PROVIDERS[$playlist1Fixture['provider']], $data['provider']);
        $this->assertSame($playlist1Fixture['provider_key'], $data['provider_key']);
        $this->assertSame($playlist1Fixture['title'], $data['title']);
    }

    public function testFind(): void
    {
        $query = Playlist::find();
        $this->assertInstanceOf(PlaylistQuery::class, $query);
    }
}
