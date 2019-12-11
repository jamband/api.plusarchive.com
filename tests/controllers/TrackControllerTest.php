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

use app\resources\Track;
use app\tests\Database;
use app\tests\RequestHelper;
use app\tests\TestCase;
use Yii;
use yii\web\NotFoundHttpException;

class TrackControllerTest extends TestCase
{
    use RequestHelper;

    protected function setUp(): void
    {
        Database::createTable('music');
        Database::createTable('music_genre');
        Database::createTable('music_genre_assn');
    }

    public function testActionIndex(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Track::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Track::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Track::PROVIDER_BANDCAMP, 'key2', 'title2', 'image2', Track::TYPE_TRACK, false, time() + 3, time()],
            ['url3', Track::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Track::TYPE_PLAYLIST, false, time() + 1, time()],
        ]);

        $data = $this->request('tracks?expand=genres');
        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('title2', $data['items'][0]['title']);
        $this->assertSame('title1', $data['items'][1]['title']);
    }

    public function testActionIndexWithProvider(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Track::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Track::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Track::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Track::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Track::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Track::TYPE_TRACK, false, time() + 3, time()],
        ]);

        $data = $this->request('tracks?expand=genres&provider=Bandcamp');
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('title1', $data['items'][0]['title']);

        $data = $this->request('tracks?expand=genres&provider=SoundCloud');
        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('title3', $data['items'][0]['title']);
        $this->assertSame('title2', $data['items'][1]['title']);
    }

    public function testActionIndexWithGenre(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Track::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Track::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Track::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Track::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Track::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Track::TYPE_TRACK, false, time() + 3, time()],
        ]);

        Database::seeder('music_genre', ['id'], [
            ['genre1', 3, time(), time()],
            ['genre2', 3, time(), time()],
        ]);

        Database::seeder('music_genre_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->request('tracks?expand=genres&genre=genre1');
        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('title1', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][0]['genres'][1]['name']);
        $this->assertSame('title2', $data['items'][1]['title']);
        $this->assertSame('genre1', $data['items'][1]['genres'][0]['name']);
    }

    public function testActionIndexWithProviderAndGenre(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Track::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Track::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Track::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Track::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Track::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Track::TYPE_TRACK, false, time() + 3, time()],
        ]);

        Database::seeder('music_genre', ['id'], [
            ['genre1', 3, time(), time()],
            ['genre2', 3, time(), time()],
        ]);

        Database::seeder('music_genre_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->request('tracks?expand=genres&provider=Bandcamp&genre=genre1');
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('title1', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][0]['genres'][1]['name']);

        $data = $this->request('tracks?expand=genres&provider=SoundCloud&genre=genre1');
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('title2', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
    }

    public function testActionIndexWithSearch(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Track::PROVIDER_BANDCAMP, 'key1', 'foo', 'image1', Track::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Track::PROVIDER_BANDCAMP, 'key2', 'bar', 'image2', Track::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Track::PROVIDER_BANDCAMP, 'key3', 'baz', 'image3', Track::TYPE_TRACK, false, time() + 3, time()],
        ]);

        $data = $this->request('tracks?expand=genres&search=o');
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('foo', $data['items'][0]['title']);

        $data = $this->request('tracks?expand=genres&search=ba');
        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('bar', $data['items'][0]['title']);
        $this->assertSame('baz', $data['items'][1]['title']);
    }

    public function testActionView(): void
    {
        Database::seeder('music', ['id'], [
            ['url1', Track::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Track::TYPE_TRACK, false, time(), time()],
        ]);

        $data = $this->request('tracks/'.Yii::$app->hashids->encode(1));
        $this->assertSame('url1', $data['url']);
    }

    public function testActionViewNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->request('tracks/'.Yii::$app->hashids->encode(1));
    }

    public function testFavorites(): void
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

        $data = $this->request('tracks/favorites?expand=genres');
        $this->assertSame('title2', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][0]['genres'][1]['name']);

        $this->assertSame('title4', $data['items'][1]['title']);
        $this->assertSame('genre1', $data['items'][1]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][1]['genres'][1]['name']);

        $this->assertSame('title5', $data['items'][2]['title']);
    }

    public function testActionMinimalGenres(): void
    {
        Database::seeder('music_genre', ['id'], [
            ['genre1', 3, time(), time()],
            ['genre2', 10, time(), time()],
            ['genre3', 5, time(), time()],
            ['genre4', 9, time(), time()],
            ['genre5', 8, time(), time()],
        ]);

        $data = $this->request('tracks/minimal-genres?limit=3');
        $expected = ['genre2', 'genre4', 'genre5'];
        $this->assertSame($expected, $data);
    }

    public function testActionResources(): void
    {
        Database::seeder('music_genre', ['id'], [
            ['genre1', 1, time(), time()],
            ['genre2', 1, time(), time()],
            ['genre3', 1, time(), time()],
        ]);

        $data = $this->request('tracks/resources');
        $expected = ['genre1', 'genre2', 'genre3'];
        $this->assertSame($expected, $data['genres']);
    }
}
