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

namespace app\tests\queries;

use app\queries\ActiveQueryTrait;
use app\tests\Database;
use app\tests\TestCase;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class ActiveQueryTraitTest extends TestCase
{
    public function setUp(): void
    {
        $database = new class extends Database
        {
            protected const SCHEMA = [
                'resource' => [
                    'id' => 'INTEGER PRIMARY KEY',
                    'name' => 'TEXT NOT NULL',
                    'created_at' => 'INTEGER NOT NULL',
                    'updated_at' => 'INTEGER NOT NULL',
                ],
            ];
        };

        $database::createTable('resource');
    }

    public function testLatest(): void
    {
        Database::seeder('resource', ['id'], [
            ['name1', time() + 1, time() + 3],
            ['name2', time() + 3, time() + 1],
            ['name3', time() + 2, time() + 2],
        ]);

        $data = Resource::find()->latest()->all();
        $this->assertSame('name2', $data[0]->name);
        $this->assertSame('name3', $data[1]->name);
        $this->assertSame('name1', $data[2]->name);

        $data = Resource::find()->latest('updated_at')->all();
        $this->assertSame('name1', $data[0]->name);
        $this->assertSame('name3', $data[1]->name);
        $this->assertSame('name2', $data[2]->name);
    }
}

class Resource extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'resource';
    }

    public static function find(): ResourceQuery
    {
        return new ResourceQuery(static::class);
    }
}

class ResourceQuery extends ActiveQuery
{
    use ActiveQueryTrait;
}
