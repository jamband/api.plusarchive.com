<?php

declare(strict_types=1);

namespace app\tests\feature\track;

use app\controllers\track\IndexController;
use app\models\Music;
use app\tests\Database;
use app\tests\feature\TestCase;
use Yii;

/** @see IndexController */
class IndexControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('music');
        $this->db->createTable('music_genre');
        $this->db->createTable('music_genre_assn');

        parent::setUp();
    }

    public function test(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'title2', 'image2', Music::TYPE_TRACK, false, time() + 3, time()],
            ['url3', Music::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Music::TYPE_PLAYLIST, false, time() + 1, time()],
        ]);

        $data = $this->request('GET', '/tracks?expand=genres');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('title2', $data['items'][0]['title']);
        $this->assertSame('title1', $data['items'][1]['title']);
    }

    public function testWithProviderParameters(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Music::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Music::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Music::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Music::TYPE_TRACK, false, time() + 3, time()],
        ]);

        $data = $this->request('GET', '/tracks?expand=genres&provider=Bandcamp');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('title1', $data['items'][0]['title']);

        $data = $this->request('GET', '/tracks?expand=genres&provider=SoundCloud');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('title3', $data['items'][0]['title']);
        $this->assertSame('title2', $data['items'][1]['title']);
    }

    public function testWithGenreParameters(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Music::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Music::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Music::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Music::TYPE_TRACK, false, time() + 3, time()],
        ]);

        $this->db->seeder('music_genre', ['id'], [
            ['genre1', 3, time(), time()],
            ['genre2', 3, time(), time()],
        ]);

        $this->db->seeder('music_genre_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->request('GET', '/tracks?expand=genres&genre=genre1');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('title1', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][0]['genres'][1]['name']);
        $this->assertSame('title2', $data['items'][1]['title']);
        $this->assertSame('genre1', $data['items'][1]['genres'][0]['name']);
    }

    public function testWithNotExistGenreParameters(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time(), time()],
        ]);

        $this->db->seeder('music_genre', ['id'], [
            ['genre1', 1, time(), time()],
        ]);

        $this->db->seeder('music_genre_assn', [], [
            [1, 1],
        ]);

        $data = $this->request('GEt', '/tracks?expand=genres&genre=genre1');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(1, $data['_meta']['totalCount']);

        $data = $this->request('GET', '/tracks?expand=genres&genre=genre2');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(0, $data['_meta']['totalCount']);
    }

    public function testWithProviderAndGenreParameters(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Music::PROVIDER_SOUNDCLOUD, 'key2', 'title2', 'image2', Music::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Music::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Music::TYPE_TRACK, false, time() + 3, time()],
        ]);

        $this->db->seeder('music_genre', ['id'], [
            ['genre1', 3, time(), time()],
            ['genre2', 3, time(), time()],
        ]);

        $this->db->seeder('music_genre_assn', [], [
            [1, 1],
            [1, 2],
            [2, 1],
            [3, 2],
        ]);

        $data = $this->request('GET', '/tracks?expand=genres&provider=Bandcamp&genre=genre1');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('title1', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][0]['genres'][1]['name']);

        $data = $this->request('GET', '/tracks?expand=genres&provider=SoundCloud&genre=genre1');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('title2', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
    }
}
