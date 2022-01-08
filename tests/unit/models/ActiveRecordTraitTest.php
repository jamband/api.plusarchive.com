<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\ActiveRecordTrait;
use app\tests\Database;
use app\tests\TestCase;
use yii\db\ActiveRecord;
use yii\test\ActiveFixture;

/** @see ActiveRecordTrait */
class ActiveRecordTraitTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;

        $this->db->createTable(Foo::tableName(), [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'country' => 'TEXT NOT NULL',
        ]);
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
        $this->assertSame($expected, Foo::names());
    }

    public function testCountries(): void
    {
        $this->getFixture('activeRecordTrait')->load();

        $expected = ['country1', 'country2', 'country3'];
        $this->assertSame($expected, Foo::countries());
    }

    public function testHasName(): void
    {
        $this->getFixture('activeRecordTrait')->load();

        $names = Foo::names();
        $this->assertSame(true, Foo::hasName($names[0]));
        $this->assertSame(true, Foo::hasName($names[1]));
        $this->assertSame(true, Foo::hasName($names[2]));
        $this->assertSame(false, Foo::hasName('name4'));
    }
}

class Foo extends ActiveRecord
{
    use ActiveRecordTrait;

    public static function tableName(): string
    {
        return 'foo';
    }
}

class ActiveRecordTraitFixture extends ActiveFixture
{
    public $modelClass = Foo::class;

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
