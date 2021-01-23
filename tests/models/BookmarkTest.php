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

namespace app\tests\resources;

use app\queries\BookmarkQuery;
use app\models\Bookmark;
use app\tests\Database;
use app\tests\TestCase;
use creocoder\taggable\TaggableBehavior;

class BookmarkTest extends TestCase
{
    public function setUp(): void
    {
        Database::createTable('bookmark');
        Database::createTable('bookmark_tag');
        Database::createTable('bookmark_tag_assn');
    }

    public function testTableName(): void
    {
        $this->assertSame('bookmark', Bookmark::tableName());
    }

    public function testFields(): void
    {
        Database::seeder('bookmark', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
        ]);

        $data = Bookmark::findOne(1)->toArray();
        $this->assertSame('name1', $data['name']);
        $this->assertSame('country1', $data['country']);
        $this->assertSame('url1', $data['url']);
        $this->assertSame('link1', $data['link']);
        $this->assertArrayNotHasKey('created_at', $data);
        $this->assertArrayNotHasKey('updated_at', $data);
    }

    public function testFind(): void
    {
        $query = Bookmark::find();
        $this->assertInstanceOf(BookmarkQuery::class, $query);
    }

    public function testGetTags(): void
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
        $this->assertSame('tag1', $data[0]->tags[0]->name);
    }

    public function testTrait(): void
    {
        $resource = new Bookmark;
        $this->assertTrue($resource->hasMethod('names'));
        $this->assertTrue($resource->hasMethod('countries'));
    }

    public function testBehaviors(): void
    {
        $resource = new Bookmark;
        $this->assertArrayHasKey('taggable', $resource->behaviors);
        $this->assertInstanceOf(TaggableBehavior::class, $resource->behaviors['taggable']);
    }
}
