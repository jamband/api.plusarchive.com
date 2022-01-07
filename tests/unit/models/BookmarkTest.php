<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Bookmark;
use app\models\BookmarkTag;
use app\queries\BookmarkQuery;
use app\tests\Database;
use app\tests\TestCase;
use app\tests\unit\fixtures\bookmark\BookmarkFixture;
use creocoder\taggable\TaggableBehavior;

/** @see Bookmark */
class BookmarkTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(Bookmark::tableName());
        $this->db->createTable(BookmarkTag::tableName());
        $this->db->createTable(BookmarkTag::tableName().'_assn');
    }

    public function fixtures(): array
    {
        return [
            'bookmark' => BookmarkFixture::class,
        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('bookmark', Bookmark::tableName());
    }

    public function testFields(): void
    {
        /** @var BookmarkFixture $fixture */
        $fixture = $this->getFixture('bookmark');
        $fixture->load();
        $bookmark1Fixture = $fixture->data['bookmark1'];

        $data = Bookmark::findOne(1)->toArray();
        $this->assertCount(4, $data);
        $this->assertSame($bookmark1Fixture['name'], $data['name']);
        $this->assertSame($bookmark1Fixture['country'], $data['country']);
        $this->assertSame($bookmark1Fixture['url'], $data['url']);
        $this->assertSame($bookmark1Fixture['link'], $data['link']);
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
