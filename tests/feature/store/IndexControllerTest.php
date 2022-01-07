<?php

declare(strict_types=1);

namespace app\tests\feature\store;

use app\controllers\store\IndexController;
use app\models\Store;
use app\models\StoreTag;
use app\tests\Database;
use app\tests\feature\TestCase;

/** @see IndexController */
class IndexControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(Store::tableName());
        $this->db->createTable(StoreTag::tableName());
        $this->db->createTable(StoreTag::tableName().'_assn');
    }

    public function test(): void
    {
        $this->db->seeder('store', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time() + 2, time()],
            ['name2', 'country2', 'url2', 'link2', time() + 1, time()],
            ['name3', 'country3', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->endpoint('GET /stores?expand=tags');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(3, $data['_meta']['totalCount']);
        $this->assertSame('name3', $data['items'][0]['name']);
        $this->assertSame('name1', $data['items'][1]['name']);
        $this->assertSame('name2', $data['items'][2]['name']);
    }

    public function testWithCountryParameters(): void
    {
        $this->db->seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->endpoint('GET /stores?expand=tags&country=foo');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('name3', $data['items'][0]['name']);
        $this->assertSame('name1', $data['items'][1]['name']);

        $data = $this->endpoint('GET /stores?expand=tags&country=bar');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name2', $data['items'][0]['name']);
    }

    public function testWithTagParameters(): void
    {
        $this->db->seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        $this->db->seeder('store_tag', ['id'], [
            ['tag1', 3, time(), time()],
            ['tag2', 3, time(), time()],
        ]);

        $this->db->seeder('store_tag_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->endpoint('GET /stores?expand=tags&tag=tag1');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('name1', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
        $this->assertSame('tag2', $data['items'][0]['tags'][1]['name']);
        $this->assertSame('name2', $data['items'][1]['name']);
        $this->assertSame('tag1', $data['items'][1]['tags'][0]['name']);
    }

    public function testWithNotExistTagParameters(): void
    {
        $this->db->seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
        ]);

        $this->db->seeder('store_tag', ['id'], [
            ['tag1', 1, time(), time()],
        ]);

        $this->db->seeder('store_tag_assn', [], [
            [1, 1],
        ]);

        $data = $this->endpoint('GET /stores?expand=tags&tag=tag1');
        $this->assertSame(200, $this->response->statusCode);
        $this->assertSame(1, $data['_meta']['totalCount']);

        $data = $this->endpoint('GET /stores?expand=tags&tag=tag2');
        $this->assertSame(200, $this->response->statusCode);
        $this->assertSame(0, $data['_meta']['totalCount']);
    }

    public function testWithCountryAndTagParameters(): void
    {
        $this->db->seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        $this->db->seeder('store_tag', ['id'], [
            ['tag1', 3, time(), time()],
            ['tag2', 3, time(), time()],
        ]);

        $this->db->seeder('store_tag_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->endpoint('GET /stores?expand=tags&country=foo&tag=tag1');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name1', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
        $this->assertSame('tag2', $data['items'][0]['tags'][1]['name']);

        $data = $this->endpoint('GET /stores?expand=tags&country=bar&tag=tag1');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name2', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
    }
}
