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

namespace app\tests\controllers\track;

use app\models\Track;
use app\tests\Database;
use app\tests\WebTestCase;
use Yii;
use yii\web\BadRequestHttpException;

class SearchControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::createTable('music');
        Database::createTable('music_genre');
        Database::createTable('music_genre_assn');

        parent::setUp();
    }

    public function testBadRequest(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->request('GET', '/track/search');
    }

    public function test(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Track::PROVIDER_BANDCAMP, 'key1', 'foo', 'image1', Track::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Track::PROVIDER_BANDCAMP, 'key2', 'bar', 'image2', Track::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Track::PROVIDER_BANDCAMP, 'key3', 'baz', 'image3', Track::TYPE_TRACK, false, time() + 3, time()],
        ]);

        $data = $this->request('GET', '/tracks/search?expand=genres&q=o');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('foo', $data['items'][0]['title']);

        $data = $this->request('GET', '/tracks/search?expand=genres&q=ba');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('bar', $data['items'][0]['title']);
        $this->assertSame('baz', $data['items'][1]['title']);
    }
}
