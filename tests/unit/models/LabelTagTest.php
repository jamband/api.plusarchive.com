<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\LabelTag;
use app\tests\Database;
use app\tests\unit\fixtures\label\LabelTagFixture;
use PHPUnit\Framework\TestCase;
use yii\test\FixtureTrait;

/** @see LabelTag */
class LabelTagTest extends TestCase
{
    use FixtureTrait;

    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(LabelTag::tableName());
    }

    public function fixtures(): array
    {
        return [
            'tag' => LabelTagFixture::class,
        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('label_tag', LabelTag::tableName());
    }

    public function testFields(): void
    {
        /** @var LabelTagFixture $fixture */
        $fixture = $this->getFixture('tag');
        $fixture->load();
        $tag1Fixture = $fixture->data['tag1'];

        $data = LabelTag::findOne(1)->toArray();
        $this->assertCount(1, $data);
        $this->assertSame($tag1Fixture['name'], $data['name']);
    }

    public function testTrait(): void
    {
        $tag = new LabelTag;
        $this->assertTrue($tag->hasMethod('names'));
        $this->assertTrue($tag->hasMethod('countries'));
    }
}
