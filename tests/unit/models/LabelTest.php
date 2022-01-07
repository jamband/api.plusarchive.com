<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Label;
use app\models\LabelTag;
use app\queries\LabelQuery;
use app\tests\Database;
use app\tests\TestCase;
use creocoder\taggable\TaggableBehavior;

/** @see Label */
class LabelTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(Label::tableName());
        $this->db->createTable(LabelTag::tableName());
        $this->db->createTable(LabelTag::tableName().'_assn');
    }

    public function testTableName(): void
    {
        $this->assertSame('label', Label::tableName());
    }

    public function testFields(): void
    {
        $this->db->seeder('label', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
        ]);

        $data = Label::findOne(1)->toArray();
        $this->assertSame('name1', $data['name']);
        $this->assertSame('country1', $data['country']);
        $this->assertSame('url1', $data['url']);
        $this->assertSame('link1', $data['link']);
        $this->assertArrayNotHasKey('created_at', $data);
        $this->assertArrayNotHasKey('updated_at', $data);
    }

    public function testFind(): void
    {
        $query = Label::find();
        $this->assertInstanceOf(LabelQuery::class, $query);
    }

    public function testGetTags(): void
    {
        $this->db->seeder('label', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
        ]);

        $this->db->seeder('label_tag', ['id'], [
            ['tag1', 1, time(), time()],
        ]);

        $this->db->seeder('label_tag_assn', [], [
            [1, 1],
        ]);

        $data = Label::find()->all();
        $this->assertSame('tag1', $data[0]->tags[0]->name);
    }

    public function testTrait(): void
    {
        $model = new Label;
        $this->assertTrue($model->hasMethod('names'));
        $this->assertTrue($model->hasMethod('countries'));
    }

    public function testBehaviors(): void
    {
        $model = new Label;
        $this->assertArrayHasKey('taggable', $model->behaviors);
        $this->assertInstanceOf(TaggableBehavior::class, $model->behaviors['taggable']);
    }
}
