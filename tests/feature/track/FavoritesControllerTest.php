<?php

declare(strict_types=1);

namespace app\tests\feature\track;

use app\controllers\track\FavoritesController;
use app\models\Music;
use app\models\MusicGenre;
use app\tests\Database;
use app\tests\feature\TestCase;

/** @see FavoritesController */
class FavoritesControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(Music::tableName());
        $this->db->createTable(MusicGenre::tableName());
        $this->db->createTable(MusicGenre::tableName().'_assn');
    }

    public function test(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time(), time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'title2', 'image2', Music::TYPE_TRACK, true, time(), time()],
            ['url3', Music::PROVIDER_BANDCAMP, 'key3', 'title3', 'image3', Music::TYPE_TRACK, false, time(), time()],
            ['url4', Music::PROVIDER_BANDCAMP, 'key4', 'title4', 'image4', Music::TYPE_TRACK, true, time(), time()],
            ['url5', Music::PROVIDER_BANDCAMP, 'key5', 'title5', 'image5', Music::TYPE_TRACK, true, time(), time()],
        ]);

        $this->db->seeder('music_genre', ['id'], [
            ['genre1', 2, time(), time()],
            ['genre2', 2, time(), time()],
        ]);

        $this->db->seeder('music_genre_assn', [], [
            [2, 1],
            [2, 2],
            [4, 1],
            [4, 2],
        ]);

        $data = $this->endpoint('GET /tracks/favorites?expand=genres');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame('title2', $data['items'][0]['title']);
        $this->assertSame('genre1', $data['items'][0]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][0]['genres'][1]['name']);

        $this->assertSame('title4', $data['items'][1]['title']);
        $this->assertSame('genre1', $data['items'][1]['genres'][0]['name']);
        $this->assertSame('genre2', $data['items'][1]['genres'][1]['name']);

        $this->assertSame('title5', $data['items'][2]['title']);
    }
}
