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

use app\models\ActiveRecordTrait;
use app\tests\Database;
use app\tests\TestCase;
use yii\db\ActiveRecord;

class ActiveRecordTraitTest extends TestCase
{
    private $model;

    public function setUp(): void
    {
        $this->model = new class extends ActiveRecord
        {
            use ActiveRecordTrait;

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
        $this->assertSame($expected, $this->model::names());
    }

    public function testCountries(): void
    {
        Database::seeder('resource', ['id'], [
            ['name3', 'country3'],
            ['name1', 'country1'],
            ['name2', 'country2'],
        ]);

        $expected = ['country1', 'country2', 'country3'];
        $this->assertSame($expected, $this->model::countries());
    }

    public function testHasName(): void
    {
        Database::seeder('resource', ['id'], [
            ['name1', 'country1'],
            ['name2', 'country2'],
            ['name3', 'country3'],
        ]);

        $names = $this->model::names();
        $this->assertSame(true, $this->model::hasName($names[0]));
        $this->assertSame(true, $this->model::hasName($names[1]));
        $this->assertSame(true, $this->model::hasName($names[2]));
        $this->assertSame(false, $this->model::hasName('name4'));
    }
}
