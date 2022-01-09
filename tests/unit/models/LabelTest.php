<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Label;
use app\models\LabelTag;
use app\queries\LabelQuery;
use app\tests\Database;
use app\tests\unit\fixtures\label\LabelFixture;
use app\tests\unit\fixtures\label\LabelTagFixture;
use creocoder\taggable\TaggableBehavior;
use PHPUnit\Framework\TestCase;
use yii\test\FixtureTrait;

/** @see Label */
class LabelTest extends TestCase
{
    use FixtureTrait;

    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(Label::tableName());
        $this->db->createTable(LabelTag::tableName());
        $this->db->createTable(LabelTag::tableName().'_assn');
    }

    public function fixtures(): array
    {
        return [
            'label' => LabelFixture::class,
            'tag' => LabelTagFixture::class,
        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('label', Label::tableName());
    }

    public function testFields(): void
    {
        /** @var LabelFixture $fixture */
        $fixture = $this->getFixture('label');
        $fixture->load();
        $label1Fixture = $fixture->data['label1'];

        $data = Label::findOne(1)->toArray();
        $this->assertCount(4, $data);
        $this->assertSame($label1Fixture['name'], $data['name']);
        $this->assertSame($label1Fixture['country'], $data['country']);
        $this->assertSame($label1Fixture['url'], $data['url']);
        $this->assertSame($label1Fixture['link'], $data['link']);
    }

    public function testFind(): void
    {
        $query = Label::find();
        $this->assertInstanceOf(LabelQuery::class, $query);
    }

    public function testGetTags(): void
    {
        $this->loadFixtures();

        /** @var LabelTagFixture $fixture */
        $fixture = $this->getFixture('tag');
        $tag1Fixture = $fixture->data['tag1'];

        $data = Label::find()->all();
        $this->assertSame($tag1Fixture['name'], $data[0]->tags[0]->name);
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
