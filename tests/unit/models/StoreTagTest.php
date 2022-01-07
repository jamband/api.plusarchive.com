<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\StoreTag;
use app\tests\Database;
use app\tests\TestCase;

/** @see StoreTag */
class StoreTagTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(StoreTag::tableName());
    }

    public function testTableName(): void
    {
        $this->assertSame('store_tag', StoreTag::tableName());
    }

    public function testFields(): void
    {
        $this->db->seeder('store_tag', ['id'], [
            ['name1', 1, time(), time()],
        ]);

        $data = StoreTag::findOne(1)->toArray();
        $this->assertArrayNotHasKey('id', $data);
        $this->assertSame('name1', $data['name']);
        $this->assertArrayNotHasKey('frequency', $data);
        $this->assertArrayNotHasKey('created_at', $data);
        $this->assertArrayNotHasKey('updated_at', $data);
    }

    public function testTrait(): void
    {
        $tag = new StoreTag;
        $this->assertTrue($tag->hasMethod('names'));
        $this->assertTrue($tag->hasMethod('countries'));
    }
}
