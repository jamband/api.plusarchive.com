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

use app\models\LabelTag;
use app\tests\Database;
use app\tests\TestCase;

class LabelTagTest extends TestCase
{
    public function setUp(): void
    {
        Database::createTable('label_tag');
    }

    public function testTableName(): void
    {
        $this->assertSame('label_tag', LabelTag::tableName());
    }

    public function testFields(): void
    {
        Database::seeder('label_tag', ['id'], [
            ['name1', 1, time(), time()],
        ]);

        $data = LabelTag::findOne(1)->toArray();
        $this->assertArrayNotHasKey('id', $data);
        $this->assertSame('name1', $data['name']);
        $this->assertArrayNotHasKey('frequency', $data);
        $this->assertArrayNotHasKey('created_at', $data);
        $this->assertArrayNotHasKey('updated_at', $data);
    }

    public function testTrait(): void
    {
        $tag = new LabelTag;
        $this->assertTrue($tag->hasMethod('names'));
        $this->assertTrue($tag->hasMethod('countries'));
    }
}
