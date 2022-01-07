<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\ActiveRecordTrait;
use app\tests\Database;
use app\tests\TestCase;
use Yii;
use yii\db\ActiveRecord;
use yii\test\ActiveFixture;

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
            ->createTable($this->model::tableName(), $columns)
            ->execute();
    }

    public function fixtures(): array
    {
        return [
            'activeRecordTrait' => ActiveRecordTraitFixture::class,
        ];
    }

    public function testNames(): void
    {
        $this->getFixture('activeRecordTrait')->load();

        $expected = ['name1', 'name2', 'name3'];
        $this->assertSame($expected, $this->model::names());
    }

    public function testCountries(): void
    {
        $this->getFixture('activeRecordTrait')->load();

        $expected = ['country1', 'country2', 'country3'];
        $this->assertSame($expected, $this->model::countries());
    }

    public function testHasName(): void
    {
        $this->getFixture('activeRecordTrait')->load();

        $names = $this->model::names();
        $this->assertSame(true, $this->model::hasName($names[0]));
        $this->assertSame(true, $this->model::hasName($names[1]));
        $this->assertSame(true, $this->model::hasName($names[2]));
        $this->assertSame(false, $this->model::hasName('name4'));
    }
}

class ActiveRecordTraitFixture extends ActiveFixture
{
    public $tableName = 'foo';

    protected function getData(): array
    {
        return [
            [
                'name' => 'name3',
                'country' => 'country3',
            ],
            [
                'name' => 'name1',
                'country' => 'country1',
            ],
            [
                'name' => 'name2',
                'country' => 'country2',
            ],
        ];
    }
}
