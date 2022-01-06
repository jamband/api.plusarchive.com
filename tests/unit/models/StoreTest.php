<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Store;
use app\queries\StoreQuery;
use app\tests\Database;
use app\tests\TestCase;
use creocoder\taggable\TaggableBehavior;

class StoreTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('store');
        $this->db->createTable('store_tag');
        $this->db->createTable('store_tag_assn');

        parent::setUp();
    }

    public function testTableName(): void
    {
        $this->assertSame('store', Store::tableName());
    }

    public function testFields(): void
    {
        $this->db->seeder('store', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
        ]);

        $data = Store::findOne(1)->toArray();
        $this->assertSame('name1', $data['name']);
        $this->assertSame('country1', $data['country']);
        $this->assertSame('url1', $data['url']);
        $this->assertSame('link1', $data['link']);
        $this->assertArrayNotHasKey('created_at', $data);
        $this->assertArrayNotHasKey('updated_at', $data);
    }

    public function testFind(): void
    {
        $query = Store::find();
        $this->assertInstanceOf(StoreQuery::class, $query);
    }

    public function testGetTags(): void
    {
        $this->db->seeder('store', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
        ]);

        $this->db->seeder('store_tag', ['id'], [
            ['tag1', 1, time(), time()],
        ]);

        $this->db->seeder('store_tag_assn', [], [
            [1, 1],
        ]);

        $data = Store::find()->all();
        $this->assertSame('tag1', $data[0]->tags[0]->name);
    }

    public function testTrait(): void
    {
        $model = new Store;
        $this->assertTrue($model->hasMethod('names'));
        $this->assertTrue($model->hasMethod('countries'));
    }

    public function testBehaviors(): void
    {
        $model = new Store;
        $this->assertArrayHasKey('taggable', $model->behaviors);
        $this->assertInstanceOf(TaggableBehavior::class, $model->behaviors['taggable']);
    }
}