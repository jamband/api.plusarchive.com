<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\StoreTag;
use app\tests\Database;
use app\tests\TestCase;
use app\tests\unit\fixtures\store\StoreFixture;
use app\tests\unit\fixtures\store\StoreTagAssnFixture;
use app\tests\unit\fixtures\store\StoreTagFixture;

/** @see StoreTag */
class StoreTagTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(StoreTag::tableName());
    }

    public function fixtures(): array
    {
        return [
            'store' => StoreFixture::class,
            'tag' => StoreTagFixture::class,
            'tagAssn' => StoreTagAssnFixture::class,
        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('store_tag', StoreTag::tableName());
    }

    public function testFields(): void
    {
        /** @var StoreTagFixture $fixture */
        $fixture = $this->getFixture('tag');
        $fixture->load();
        $tag1Fixture = $fixture->data['tag1'];

        $data = StoreTag::findOne(1)->toArray();
        $this->assertCount(1, $data);
        $this->assertSame($tag1Fixture['name'], $data['name']);
    }

    public function testTrait(): void
    {
        $tag = new StoreTag;
        $this->assertTrue($tag->hasMethod('names'));
        $this->assertTrue($tag->hasMethod('countries'));
    }
}
