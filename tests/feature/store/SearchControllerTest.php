<?php

declare(strict_types=1);

namespace app\tests\feature\store;

use app\controllers\store\SearchController;
use app\tests\Database;
use app\tests\feature\TestCase;
use Yii;
use yii\web\BadRequestHttpException;

/** @see SearchController */
class SearchControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('store');
        $this->db->createTable('store_tag');
        $this->db->createTable('store_tag_assn');

        parent::setUp();
    }

    public function testBadRequest(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->request('GET', '/stores/search');
    }

    public function test(): void
    {
        $this->db->seeder('store', ['id'], [
            ['foo', 'country1', 'url1', 'link1', time() + 2, time()],
            ['bar', 'country2', 'url2', 'link2', time() + 1, time()],
            ['baz', 'country3', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->request('GET', '/stores/search?expand=tags&q=o');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('foo', $data['items'][0]['name']);

        $data = $this->request('GET', '/stores/search?expand=tags&q=ba');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('bar', $data['items'][0]['name']);
        $this->assertSame('baz', $data['items'][1]['name']);
    }
}
