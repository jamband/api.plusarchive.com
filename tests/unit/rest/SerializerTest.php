<?php

declare(strict_types=1);

namespace app\tests\unit\rest;

use app\rest\Serializer;
use app\tests\Database;
use app\tests\feature\TestCase;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\test\ActiveFixture;
use yii\test\FixtureTrait;

/** @see Serializer */
class SerializerTest extends TestCase
{
    use FixtureTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;

        $this->db->createTable(Foo::tableName(), [
            'id' => 'INTEGER PRIMARY KEY',
            'content' => 'TEXT NOT NULL',
        ]);
    }

    public function fixtures(): array
    {
        return [
            'serializer' => SerializerFixture::class,
        ];
    }

    public function testSerializer()
    {
        $this->getFixture('serializer')->load();

        $data = (new Serializer)->serialize(new ActiveDataProvider([
            'query' => Foo::find(),
            'pagination' => [
                'route' => '/',
                'pageSize' => 2,
            ],
        ]));

        $this->assertCount(2, $data);
        $this->assertArrayHasKey('items', $data);

        $this->assertArrayHasKey('_meta', $data);
        $this->assertCount(4, $data['_meta']);
        $this->assertSame(5, $data['_meta']['totalCount']);
        $this->assertSame(3, $data['_meta']['pageCount']);
        $this->assertSame(1, $data['_meta']['currentPage']);
        $this->assertSame(2, $data['_meta']['perPage']);

        $headers = $this->response->headers;
        $this->assertCount(5, $headers);
        $this->assertSame(5, $headers['x-pagination-total-count']);
        $this->assertSame(3, $headers['x-pagination-page-count']);
        $this->assertSame(1, $headers['x-pagination-current-page']);
        $this->assertSame(2, $headers['x-pagination-per-page']);
        $this->assertArrayHasKey('link', $headers);
    }
}

class SerializerFixture extends ActiveFixture
{
    public $modelClass = Foo::class;

    protected function getData(): array
    {
        return [
            'foo1' => ['content' => 'foo1'],
            'foo2' => ['content' => 'foo2'],
            'foo3' => ['content' => 'foo3'],
            'foo4' => ['content' => 'foo4'],
            'foo5' => ['content' => 'foo5'],
        ];
    }
}

class Foo extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'foo';
    }
}
