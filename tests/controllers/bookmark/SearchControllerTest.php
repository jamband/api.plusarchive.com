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

namespace app\tests\controllers\bookmark;

use app\tests\Database;
use app\tests\WebTestCase;
use Yii;
use yii\web\BadRequestHttpException;

class SearchControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::createTable('bookmark');
        Database::createTable('bookmark_tag');
        Database::createTable('bookmark_tag_assn');

        parent::setUp();
    }

    public function testBadRequest(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->request('GET', '/bookmarks/search');
    }

    public function test(): void
    {
        Database::seeder('bookmark', ['id'], [
            ['foo', 'country1', 'url1', 'link1', time() + 2, time()],
            ['bar', 'country2', 'url2', 'link2', time() + 1, time()],
            ['baz', 'country3', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->request('GET', '/bookmarks/search?expand=tags&q=o');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('foo', $data['items'][0]['name']);

        $data = $this->request('GET', '/bookmarks/search?expand=tags&q=ba');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('bar', $data['items'][0]['name']);
        $this->assertSame('baz', $data['items'][1]['name']);
    }
}
