<?php

/*
 * This file is part of the api.plusarchive.com
 *
 * (c) Tomoki Morita <tmsongbooks215@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace app\tests\resources;

use app\queries\LabelQuery;
use app\models\Label;
use app\tests\Database;
use app\tests\TestCase;
use creocoder\taggable\TaggableBehavior;

class LabelTest extends TestCase
{
    public function setUp(): void
    {
        Database::createTable('label');
        Database::createTable('label_tag');
        Database::createTable('label_tag_assn');
    }

    public function testTableName(): void
    {
        $this->assertSame('label', Label::tableName());
    }

    public function testFields(): void
    {
        Database::seeder('label', ['id'], [
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
        Database::seeder('label', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time(), time()],
        ]);

        Database::seeder('label_tag', ['id'], [
            ['tag1', 1, time(), time()],
        ]);

        Database::seeder('label_tag_assn', [], [
            [1, 1],
        ]);

        $data = Label::find()->all();
        $this->assertSame('tag1', $data[0]->tags[0]->name);
    }

    public function testTrait(): void
    {
        $resource = new Label;
        $this->assertTrue($resource->hasMethod('names'));
        $this->assertTrue($resource->hasMethod('countries'));
    }

    public function testBehaviors(): void
    {
        $resource = new Label;
        $this->assertArrayHasKey('taggable', $resource->behaviors);
        $this->assertInstanceOf(TaggableBehavior::class, $resource->behaviors['taggable']);
    }
}
