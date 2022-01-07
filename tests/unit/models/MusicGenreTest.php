<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\MusicGenre;
use app\tests\Database;
use app\tests\TestCase;

/** @see MusicGenre */
class MusicGenreTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(MusicGenre::tableName());
    }

    public function testTableName(): void
    {
        $this->assertSame('music_genre', MusicGenre::tableName());
    }

    public function testFields(): void
    {
        $this->db->seeder('music_genre', ['id'], [
            ['name1', 1, time(), time()],
        ]);

        $data = MusicGenre::findOne(1)->toArray();
        $this->assertArrayNotHasKey('id', $data);
        $this->assertSame('name1', $data['name']);
        $this->assertArrayNotHasKey('frequency', $data);
        $this->assertArrayNotHasKey('created_at', $data);
        $this->assertArrayNotHasKey('updated_at', $data);
    }

    public function testMinimal(): void
    {
        $this->db->seeder('music_genre', ['id'], [
            ['name1', 3, time(), time()],
            ['name2', 8, time(), time()],
            ['name3', 2, time(), time()],
            ['name4', 1, time(), time()],
            ['name5', 5, time(), time()],
        ]);

        $data = MusicGenre::minimal(3);
        $this->assertSame('name1', $data[0]);
        $this->assertSame('name2', $data[1]);
        $this->assertSame('name5', $data[2]);
    }

    public function testTrait(): void
    {
        $genre = new MusicGenre;
        $this->assertTrue($genre->hasMethod('names'));
        $this->assertTrue($genre->hasMethod('countries'));
    }
}
