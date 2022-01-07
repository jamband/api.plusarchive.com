<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\ActiveRecordTrait;
use app\tests\Database;
use app\tests\TestCase;
use Yii;
use yii\db\ActiveRecord;

/** @see ActiveRecordTrait */
class ActiveRecordTraitTest extends TestCase
{
    public function setUp(): void
    {
        $this->model = new class extends ActiveRecord
        {
            use ActiveRecordTrait;

            public static function tableName(): string
            {
                return 'foo';
            }
        };

        $this->db = new Database;

        $columns = [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'country' => 'TEXT NOT NULL',
        ];

        Yii::$app->getDb()->createCommand()
            ->createTable('foo', $columns)
            ->execute();
    }

    public function testNames(): void
    {
        $this->db->seeder('foo', ['id'], [
            ['name3', 'country3'],
            ['name1', 'country1'],
            ['name2', 'country2'],
        ]);

        $expected = ['name1', 'name2', 'name3'];
        $this->assertSame($expected, $this->model::names());
    }

    public function testCountries(): void
    {
        $this->db->seeder('foo', ['id'], [
            ['name3', 'country3'],
            ['name1', 'country1'],
            ['name2', 'country2'],
        ]);

        $expected = ['country1', 'country2', 'country3'];
        $this->assertSame($expected, $this->model::countries());
    }

    public function testHasName(): void
    {
        $this->db->seeder('foo', ['id'], [
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
