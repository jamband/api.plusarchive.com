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

use app\resources\Track;
use app\tests\Database;
use app\tests\WebTestCase;
use Yii;

class FavoritesControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::createTable('music');
        Database::createTable('music_genre');
        Database::createTable('music_genre_assn');

        parent::setUp();
    }

    public function test(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Track::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Track::TYPE_TRACK, false, time(), time()],
            ['url2', Track::PROVIDER_BANDCAMP, 'key2', 'title2', 'image2', Track::TYPE_TRACK, true, time(), time()],
            ['url3', Track::PROVIDER_BANDCAMP, 'key3', 'title3', 'image3', Track::TYPE_TRACK, false, time(), time()],
            ['url4', Track::PROVIDER_BANDCAMP, 'key4', 'title4', 'image4', Track::TYPE_TRACK, true, time(), time()],
            ['url5', Track::PROVIDER_BANDCAMP, 'key5', 'title5', 'image5', Track::TYPE_TRACK, true, time(), time()],
        ]);

        Database::seeder('music_genre', ['id'], [
            ['genre1', 2, time(), time()],
            ['genre2', 2, time(), time()],
        ]);

        Database::seeder('music_genre_assn', [], [
            [2, 1],
            [2, 2],
            [4, 1],
            [4, 2],
        ]);

        $data = $this->request('GET', '/tracks/favorites?expand=genres');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame('title2', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][0]['genres'][1]['name']);

        $this->assertSame('title4', $data['items'][1]['title']);
        $this->assertSame('genre1', $data['items'][1]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][1]['genres'][1]['name']);

        $this->assertSame('title5', $data['items'][2]['title']);
    }
}
