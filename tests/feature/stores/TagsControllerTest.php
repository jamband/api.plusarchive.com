<?php

declare(strict_types=1);

namespace app\tests\feature\stores;

use app\controllers\stores\TagsController;
use app\models\Store;
use app\models\StoreTag;
use app\tests\Database;
use app\tests\feature\TestCase;

/** @see TagsController */
class TagsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(Store::tableName());
        $this->db->createTable(StoreTag::tableName());
    }

    public function test(): void
    {
        $this->db->seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
            ['name2', 'bar', 'url2', 'link2', time(), time()],
            ['name3', 'baz', 'url3', 'link3', time(), time()],
            ['name4', 'foo', 'url4', 'link4', time(), time()],
        ]);

        $this->db->seeder('store_tag', ['id'], [
            ['tag1', 1, time(), time()],
            ['tag2', 1, time(), time()],
            ['tag3', 1, time(), time()],
        ]);

        $data = $this->endpoint('GET /stores/tags');
        $this->assertSame(200, $this->response->statusCode);
        $this->assertSame(['tag1', 'tag2', 'tag3'], $data);
    }
}
