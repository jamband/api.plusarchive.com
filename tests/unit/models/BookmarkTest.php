<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Bookmark;
use app\queries\BookmarkQuery;
use app\tests\Database;
use app\tests\TestCase;
use creocoder\taggable\TaggableBehavior;

class BookmarkTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('bookmark');
        $this->db->createTable('bookmark_tag');
        $this->db->createTable('bookmark_tag_assn');

        parent::setUp();
    }

    public function testTableName(): void
    {
        $this->assertSame('bookmark', Bookmark::tableName());
    }

    public function testFields(): void
    {
        $this->db->seeder('bookmark', ['id'], [
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
        $this->db->seeder('bookmark', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
        ]);

        $this->db->seeder('bookmark_tag', ['id'], [
            ['tag1', 1, time(), time()],
        ]);

        $this->db->seeder('bookmark_tag_assn', [], [
            [1, 1],
        ]);

        $data = Bookmark::find()->all();
        $this->assertSame('tag1', $data[0]->tags[0]->name);
    }

    public function testTrait(): void
    {
        $model = new Bookmark;
        $this->assertTrue($model->hasMethod('names'));
        $this->assertTrue($model->hasMethod('countries'));
    }

    public function testBehaviors(): void
    {
        $model = new Bookmark;
        $this->assertArrayHasKey('taggable', $model->behaviors);
        $this->assertInstanceOf(TaggableBehavior::class, $model->behaviors['taggable']);
    }
}