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

namespace app\tests\models;

use app\models\MusicGenre;
use app\tests\Database;
use app\tests\TestCase;

class MusicGenreTest extends TestCase
{
    public function setUp(): void
    {
        Database::createTable('music_genre');
    }

    public function testTableName(): void
    {
        $this->assertSame('music_genre', MusicGenre::tableName());
    }

    public function testFields(): void
    {
        Database::seeder('music_genre', ['id'], [
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
        Database::seeder('music_genre', ['id'], [
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
