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

namespace app\tests\models;

use app\models\StoreTag;
use app\tests\Database;
use app\tests\TestCase;

class StoreTagTest extends TestCase
{
    public function setUp(): void
    {
        Database::createTable('store_tag');
    }

    public function testTableName(): void
    {
        $this->assertSame('store_tag', StoreTag::tableName());
    }

    public function testFields(): void
    {
        Database::seeder('store_tag', ['id'], [
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
