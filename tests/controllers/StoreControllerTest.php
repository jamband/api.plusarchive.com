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

namespace app\tests\controllers;

use app\tests\Database;
use app\tests\RequestHelper;
use app\tests\TestCase;

class storeControllerTest extends TestCase
{
    use RequestHelper;

    protected function setUp(): void
    {
        Database::createTable('store');
        Database::createTable('store_tag');
        Database::createTable('store_tag_assn');
    }

    public function testActionIndex(): void
    {
        Database::seeder('store', ['id'], [
            ['name1', 'country1', 'url1', 'link1', time() + 2, time()],
            ['name2', 'country2', 'url2', 'link2', time() + 1, time()],
            ['name3', 'country3', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->request('stores?expand=tags');
        $this->assertSame(3, $data['_meta']['totalCount']);
        $this->assertSame('name3', $data['items'][0]['name']);
        $this->assertSame('name1', $data['items'][1]['name']);
        $this->assertSame('name2', $data['items'][2]['name']);
    }

    public function testActionIndexWithCountry(): void
    {
        Database::seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->request('stores?expand=tags&country=foo');
        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('name3', $data['items'][0]['name']);
        $this->assertSame('name1', $data['items'][1]['name']);

        $data = $this->request('stores?expand=tags&country=bar');
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name2', $data['items'][0]['name']);
    }

    public function testActionIndexWithTag(): void
    {
        Database::seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        Database::seeder('store_tag', ['id'], [
            ['tag1', 3, time(), time()],
            ['tag2', 3, time(), time()],
        ]);

        Database::seeder('store_tag_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->request('stores?expand=tags&tag=tag1');
        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('name1', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
        $this->assertSame('tag2', $data['items'][0]['tags'][1]['name']);
        $this->assertSame('name2', $data['items'][1]['name']);
        $this->assertSame('tag1', $data['items'][1]['tags'][0]['name']);
    }

    public function testActionIndexWithNotExistTag(): void
    {
        Database::seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
        ]);

        Database::seeder('store_tag', ['id'], [
            ['tag1', 1, time(), time()],
        ]);

        Database::seeder('store_tag_assn', [], [
            [1, 1],
        ]);

        $data = $this->request('stores?expand=tags&tag=tag1');
        $this->assertSame(1, $data['_meta']['totalCount']);

        $data = $this->request('stores?expand=tags&tag=tag2');
        $this->assertSame(0, $data['_meta']['totalCount']);
    }

    public function testActionIndexWithCountryAndTag(): void
    {
        Database::seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time() + 2, time()],
            ['name2', 'bar', 'url2', 'link2', time() + 1, time()],
            ['name3', 'foo', 'url3', 'link3', time() + 3, time()],
        ]);

        Database::seeder('store_tag', ['id'], [
            ['tag1', 3, time(), time()],
            ['tag2', 3, time(), time()],
        ]);

        Database::seeder('store_tag_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->request('stores?expand=tags&country=foo&tag=tag1');
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name1', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
        $this->assertSame('tag2', $data['items'][0]['tags'][1]['name']);

        $data = $this->request('stores?expand=tags&country=bar&tag=tag1');
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('name2', $data['items'][0]['name']);
        $this->assertSame('tag1', $data['items'][0]['tags'][0]['name']);
    }

    public function testActionIndexWithSearch(): void
    {
        Database::seeder('store', ['id'], [
            ['foo', 'country1', 'url1', 'link1', time() + 2, time()],
            ['bar', 'country2', 'url2', 'link2', time() + 1, time()],
            ['baz', 'country3', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->request('stores?expand=tags&search=o');
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('foo', $data['items'][0]['name']);

        $data = $this->request('stores?expand=tags&search=ba');
        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('bar', $data['items'][0]['name']);
        $this->assertSame('baz', $data['items'][1]['name']);
    }

    public function testActionResources(): void
    {
        Database::seeder('store', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
            ['name2', 'bar', 'url2', 'link2', time(), time()],
            ['name3', 'baz', 'url3', 'link3', time(), time()],
            ['name4', 'foo', 'url4', 'link4', time(), time()],
        ]);

        Database::seeder('store_tag', ['id'], [
            ['tag1', 1, time(), time()],
            ['tag2', 1, time(), time()],
            ['tag3', 1, time(), time()],
        ]);

        $data = $this->request('stores/resources');

        $expected = ['bar', 'baz', 'foo'];
        $this->assertSame($expected, $data['countries']);

        $expected = ['tag1', 'tag2', 'tag3'];
        $this->assertSame($expected, $data['tags']);
    }
}
