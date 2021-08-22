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

namespace app\tests\feature\bookmark;

use app\tests\Database;
use app\tests\feature\TestCase;
use Yii;

class IndexControllerTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('bookmark');
        $this->db->createTable('bookmark_tag');
        $this->db->createTable('bookmark_tag_assn');

        parent::setUp();
    }

    public function test(): void
    {
        $this->db->seeder('bookmark', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time() + 2, time()],
            ['name2', 'country2', 'url2', 'link2', time() + 1, time()],
            ['name3', 'country3', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->request('GET', '/bookmarks?expand=tags');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(3, $data['_meta']['totalCount']);
        $this->assertSame('name3', $data['items'][0]['name']);
        $this->assertSame('name1', $data['items'][1]['name']);
        $this->assertSame('name2', $data['items'][2]['name']);
    }

    public function testWithCountryParameters(): void
    {
        $this->db->seeder('bookmark', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->request('GET', '/bookmarks?expand=tags&country=foo');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('name3', $data['items'][0]['name']);
        $this->assertSame('name1', $data['items'][1]['name']);

        $data = $this->request('GET', '/bookmarks?expand=tags&country=bar');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name2', $data['items'][0]['name']);
    }

    public function testWithTagParameters(): void
    {
        $this->db->seeder('bookmark', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        $this->db->seeder('bookmark_tag', ['id'], [
            ['tag1', 3, time(), time()],
            ['tag2', 3, time(), time()],
        ]);

        $this->db->seeder('bookmark_tag_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->request('GET', '/bookmarks?expand=tags&tag=tag1');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('name1', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
        $this->assertSame('tag2', $data['items'][0]['tags'][1]['name']);
        $this->assertSame('name2', $data['items'][1]['name']);
        $this->assertSame('tag1', $data['items'][1]['tags'][0]['name']);
    }

    public function testWithNotExistTagParameters(): void
    {
        $this->db->seeder('bookmark', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
        ]);

        $this->db->seeder('bookmark_tag', ['id'], [
            ['tag1', 1, time(), time()],
        ]);

        $this->db->seeder('bookmark_tag_assn', [], [
            [1, 1],
        ]);

        $data = $this->request('GET', '/bookmarks?expand=tags&tag=tag1');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(1, $data['_meta']['totalCount']);

        $data = $this->request('GET', '/bookmarks?expand=tags&tag=tag2');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(0, $data['_meta']['totalCount']);
    }

    public function testWithCountryAndTagParameters(): void
    {
        $this->db->seeder('bookmark', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        $this->db->seeder('bookmark_tag', ['id'], [
            ['tag1', 3, time(), time()],
            ['tag2', 3, time(), time()],
        ]);

        $this->db->seeder('bookmark_tag_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->request('GET', '/bookmarks?expand=tags&country=foo&tag=tag1');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name1', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
        $this->assertSame('tag2', $data['items'][0]['tags'][1]['name']);

        $data = $this->request('GET', '/bookmarks?expand=tags&country=bar&tag=tag1');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name2', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
    }
}
