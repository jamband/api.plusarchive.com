<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Bookmark;
use app\models\BookmarkTag;
use app\queries\BookmarkQuery;
use app\tests\Database;
use app\tests\unit\fixtures\bookmark\BookmarkFixture;
use app\tests\unit\fixtures\bookmark\BookmarkTagFixture;
use creocoder\taggable\TaggableBehavior;
use PHPUnit\Framework\TestCase;
use yii\test\FixtureTrait;

/** @see Bookmark */
class BookmarkTest extends TestCase
{
    use FixtureTrait;

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
        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('bookmark', Bookmark::tableName());
    }

    public function testFields(): void
    {
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
        $this->loadFixtures();
        $fixture = $this->getFixture('tag');
        $tag1Fixture = $fixture->data['tag1'];

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
