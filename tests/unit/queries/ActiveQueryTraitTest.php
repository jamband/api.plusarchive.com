<?php

declare(strict_types=1);

namespace app\tests\unit\queries;

use app\queries\ActiveQueryTrait;
use app\tests\Database;
use PHPUnit\Framework\TestCase;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\test\ActiveFixture;
use yii\test\FixtureTrait;

/** @see ActiveQueryTrait */
class ActiveQueryTraitTest extends TestCase
{
    use FixtureTrait;

    public function setUp(): void
    {
        $this->db = new Database;

        $this->db->createTable(FooLatest::tableName(), [
            'id' => 'INTEGER PRIMARY KEY',
            'content' => 'TEXT NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ]);

        $this->db->createTable(FooNothing::tableName(), [
            'id' => 'INTEGER PRIMARY KEY',
            'content' => 'TEXT NOT NULL',
        ]);
    }

    public function fixtures(): array
    {
        return [
            'latest' => FooLatestFixture::class,
            'nothing' => FooNothingFixture::class,
        ];
    }

    public function testLatest(): void
    {
        $this->getFixture('latest')->load();

        $data = FooLatest::find()->latest()->all();
        $this->assertSame('foo2', $data[0]->content);
        $this->assertSame('foo3', $data[1]->content);
        $this->assertSame('foo1', $data[2]->content);

        $data = FooLatest::find()->latest('updated_at')->all();
        $this->assertSame('foo1', $data[0]->content);
        $this->assertSame('foo3', $data[1]->content);
        $this->assertSame('foo2', $data[2]->content);
    }

    public function testNothing(): void
    {
        $this->getFixture('nothing')->load();

        $query = FooNothing::find();
        $this->assertSame(1, count($query->all()));
        $this->assertSame(0, count($query->nothing()->all()));
    }
}

class FooQuery extends ActiveQuery
{
    use ActiveQueryTrait;
}

class Foo extends ActiveRecord
{
    public static function find(): FooQuery
    {
        return new FooQuery(static::class);
    }
}

class FooLatest extends Foo
{
    public static function tableName(): string
    {
        return 'latest';
    }
}

class FooNothing extends Foo
{
    public static function tableName(): string
    {
        return 'nothing';
    }
}

class FooLatestFixture extends ActiveFixture
{
    public $modelClass = FooLatest::class;

    protected function getData(): array
    {
        return [
            'foo1' => [
                'content' => 'foo1',
                'created_at' => time() + 1,
                'updated_at' => time() + 3,
            ],
            'foo2' => [
                'content' => 'foo2',
                'created_at' => time() + 3,
                'updated_at' => time() + 1,
            ],
            'foo3' => [
                'content' => 'foo3',
                'created_at' => time() + 2,
                'updated_at' => time() + 2,
            ],
        ];
    }
}

class FooNothingFixture extends ActiveFixture
{
    public $modelClass = FooNothing::class;

    protected function getData(): array
    {
        return [
            'foo1' => [
                'content' => 'foo1',
            ],
        ];
    }
}
