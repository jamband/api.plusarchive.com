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

namespace app\tests\queries;

use app\models\Bookmark;
use app\tests\Database;
use app\tests\TestCase;

class BookmarkQueryTest extends TestCase
{
    public function setUp(): void
    {
        Database::createTable('bookmark');
        Database::createTable('bookmark_tag');
        Database::createTable('bookmark_tag_assn');
    }

    public function testInit(): void
    {
        Database::seeder('bookmark', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
        ]);

        Database::seeder('bookmark_tag', ['id'], [
            ['tag1', 1, time(), time()],
        ]);

        Database::seeder('bookmark_tag_assn', [], [
            [1, 1],
        ]);

        $data = Bookmark::find()->all();

        $this->assertSame(1, count($data[0]->tags));
        $this->assertSame('tag1', $data[0]->tags[0]->name);
    }

    public function testBehaviors(): void
    {
        Database::seeder('bookmark', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
            ['name2', 'country2', 'url2', 'link2', time(), time()],
            ['name3', 'country3', 'url3', 'link3', time(), time()],
        ]);

        Database::seeder('bookmark_tag', ['id'], [
            ['tag1', 2, time(), time()],
            ['tag2', 1, time(), time()],
        ]);

        Database::seeder('bookmark_tag_assn', [], [
            [1, 1],
            [2, 1],
            [3, 2],
        ]);

        $data = Bookmark::find()->allTagValues('tag1')->all();
        $this->assertSame(2, count($data));
    }

    public function testCountry(): void
    {
        Database::seeder('bookmark', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
            ['name2', 'foo', 'url2', 'link2', time(), time()],
            ['name3', 'bar', 'url3', 'link3', time(), time()],
        ]);

        $data = Bookmark::find()->country('foo')->all();
        $this->assertSame(2, count($data));

        $data = Bookmark::find()->country('bar')->all();
        $this->assertSame(1, count($data));

        $data = Bookmark::find()->country('baz')->all();
        $this->assertSame(0, count($data));
    }

    public function testSearchInNameOrder(): void
    {
        Database::seeder('bookmark', ['id'], [
            ['name3', 'country3', 'url3', 'link3', time(), time()],
            ['name1', 'country1', 'url1', 'link1', time(), time()],
            ['name2', 'country2', 'url2', 'link2', time(), time()],
            ['foo', 'country4', 'url4', 'link4', time(), time()],
        ]);

        $data = Bookmark::find()->searchInNameOrder('name')->all();
        $this->assertSame(3, count($data));
        $this->assertSame('name1', $data[0]->name);
        $this->assertSame('name2', $data[1]->name);
        $this->assertSame('name3', $data[2]->name);
    }
}
