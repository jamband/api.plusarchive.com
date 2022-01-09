<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Store;
use app\models\StoreTag;
use app\queries\StoreQuery;
use app\tests\Database;
use app\tests\unit\fixtures\store\StoreFixture;
use app\tests\unit\fixtures\store\StoreTagFixture;
use creocoder\taggable\TaggableBehavior;
use PHPUnit\Framework\TestCase;
use yii\test\FixtureTrait;

/** @see Store */
class StoreTest extends TestCase
{
    use FixtureTrait;

    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(Store::tableName());
        $this->db->createTable(StoreTag::tableName());
        $this->db->createTable(StoreTag::tableName().'_assn');
    }

    public function fixtures(): array
    {
        return [
            'store' => StoreFixture::class,
            'tag' => StoreTagFixture::class,
        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('store', Store::tableName());
    }

    public function testFields(): void
    {
        /** @var StoreFixture $fixture */
        $fixture = $this->getFixture('store');
        $fixture->load();
        $store1Fixture = $fixture->data['store1'];

        $data = Store::findOne(1)->toArray();
        $this->assertCount(4, $data);
        $this->assertSame($store1Fixture['name'], $data['name']);
        $this->assertSame($store1Fixture['country'], $data['country']);
        $this->assertSame($store1Fixture['url'], $data['url']);
        $this->assertSame($store1Fixture['link'], $data['link']);
    }

    public function testFind(): void
    {
        $query = Store::find();
        $this->assertInstanceOf(StoreQuery::class, $query);
    }

    public function testGetTags(): void
    {
        $this->loadFixtures();

        /** @var StoreTagFixture $fixture */
        $fixture = $this->getFixture('tag');
        $tag1Fixture = $fixture->data['tag1'];

        $data = Store::find()->all();
        $this->assertSame($tag1Fixture['name'], $data[0]->tags[0]->name);
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
