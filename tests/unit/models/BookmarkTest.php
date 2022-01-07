<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Bookmark;
use app\models\BookmarkTag;
use app\queries\BookmarkQuery;
use app\tests\Database;
use app\tests\TestCase;
use app\tests\unit\fixtures\bookmark\BookmarkFixture;
use app\tests\unit\fixtures\bookmark\BookmarkTagAssnFixture;
use app\tests\unit\fixtures\bookmark\BookmarkTagFixture;
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
            'tag' => BookmarkTagFixture::class,
            'tagAssn' => BookmarkTagAssnFixture::class,
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
        $this->getFixture('bookmark')->load();

        /** @var BookmarkTagFixture $fixture */
        $fixture = $this->getFixture('tag');
        $fixture->load();
        $tag1Fixture = $fixture->data['tag1'];

        $this->getFixture('tagAssn')->load();

        $data = Bookmark::find()->all();
        $this->assertSame($tag1Fixture['name'], $data[0]->tags[0]->name);
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
