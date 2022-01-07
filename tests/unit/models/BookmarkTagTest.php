<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\BookmarkTag;
use app\tests\Database;
use app\tests\TestCase;
use app\tests\unit\fixtures\BookmarkTagFixture;

/** @see BookmarkTag */
class BookmarkTagTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(BookmarkTag::tableName());
    }

    public function fixtures(): array
    {
        return [
            'tag' => BookmarkTagFixture::class,
        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('bookmark_tag', BookmarkTag::tableName());
    }

    public function testFields(): void
    {
        /** @var BookmarkTagFixture $fixture */
        $fixture = $this->getFixture('tag');
        $fixture->load();
        $tag1Fixture = $fixture->data['tag1'];

        $data = BookmarkTag::findOne(1)->toArray();
        $this->assertCount(1, $data);
        $this->assertSame($tag1Fixture['name'], $data['name']);
    }

    public function testTrait(): void
    {
        $tag = new BookmarkTag;
        $this->assertTrue($tag->hasMethod('names'));
        $this->assertTrue($tag->hasMethod('countries'));
    }
}
