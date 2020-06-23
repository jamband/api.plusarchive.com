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

use app\resources\ResourceTrait;
use app\tests\Database;
use app\tests\TestCase;
use yii\db\ActiveRecord;

class ResourceTraitTest extends TestCase
{
    private $resource;

    public function setUp(): void
    {
        $this->resource = new class extends ActiveRecord
        {
            use ResourceTrait;

            public static function tableName(): string
            {
                return 'resource';
            }
        };

        $database = new class extends Database
        {
            protected const SCHEMA = [
                'resource' => [
                    'id' => 'INTEGER PRIMARY KEY',
                    'name' => 'TEXT NOT NULL',
                    'country' => 'TEXT NOT NULL',
                ]
            ];
        };

        $database::createTable('resource');
    }

    public function testNames(): void
    {
        Database::seeder('resource', ['id'], [
            ['name3', 'country3'],
            ['name1', 'country1'],
            ['name2', 'country2'],
        ]);

        $expected = ['name1', 'name2', 'name3'];
        $this->assertSame($expected, $this->resource::names());
    }

    public function testCountries(): void
    {
        Database::seeder('resource', ['id'], [
            ['name3', 'country3'],
            ['name1', 'country1'],
            ['name2', 'country2'],
        ]);

        $expected = ['country1', 'country2', 'country3'];
        $this->assertSame($expected, $this->resource::countries());
    }

    public function testHasName(): void
    {
        Database::seeder('resource', ['id'], [
            ['name1', 'country1'],
            ['name2', 'country2'],
            ['name3', 'country3'],
        ]);

        $names = $this->resource::names();
        $this->assertSame(true, $this->resource::hasName($names[0]));
        $this->assertSame(true, $this->resource::hasName($names[1]));
        $this->assertSame(true, $this->resource::hasName($names[2]));
        $this->assertSame(false, $this->resource::hasName('name4'));
    }
}
