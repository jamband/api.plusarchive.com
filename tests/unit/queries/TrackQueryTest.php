<?php

declare(strict_types=1);

namespace app\tests\unit\queries;

use app\models\Music;
use app\models\MusicGenre;
use app\models\Track;
use app\queries\TrackQuery;
use app\tests\Database;
use app\tests\TestCase;

/** @see TrackQuery */
class TrackQueryTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(Music::tableName());
        $this->db->createTable(MusicGenre::tableName());
        $this->db->createTable(MusicGenre::tableName().'_assn');
    }

    public function testInit(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_YOUTUBE, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time(), time()],
            ['url2', Music::PROVIDER_YOUTUBE, 'key2', 'title2', 'image2', Music::TYPE_TRACK, false, time(), time()],
            ['url3', Music::PROVIDER_YOUTUBE, 'key3', 'title3', 'image3', Music::TYPE_PLAYLIST, false, time(), time()],
        ]);

        $this->db->seeder('music_genre', ['id'], [
            ['genre1', 1, time(), time()],
        ]);

        $this->db->seeder('music_genre_assn', [], [
            [1, 1],
        ]);

        $data = Track::find()->all();
        $this->assertSame(2, count($data));
        $this->assertSame(1, count($data[0]->genres));
        $this->assertSame('genre1', $data[0]->genres[0]->name);
    }

    public function testBehaviors(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time(), time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'title2', 'image2', Music::TYPE_TRACK, false, time(), time()],
            ['url3', Music::PROVIDER_BANDCAMP, 'key3', 'title3', 'image3', Music::TYPE_TRACK, false, time(), time()],
        ]);

        $this->db->seeder('music_genre', ['id'], [
            ['genre1', 2, time(), time()],
            ['genre2', 1, time(), time()],
        ]);

        $this->db->seeder('music_genre_assn', [], [
            [1, 1],
            [2, 1],
            [3, 2],
        ]);

        $data = Track::find()->allTagValues('genre1')->all();
        $this->assertSame(2, count($data));
    }

    public function testProvider(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time(), time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'title2', 'image2', Music::TYPE_TRACK, false, time(), time()],
            ['url3', Music::PROVIDER_SOUNDCLOUD, 'key3', 'title3', 'image3', Music::TYPE_TRACK, false, time(), time()],
        ]);

        $data = Track::find()->provider('Bandcamp')->all();
        $this->assertSame(2, count($data));

        $data = Track::find()->provider('SoundCloud')->all();
        $this->assertSame(1, count($data));

        $data = Track::find()->provider('YouTube')->all();
        $this->assertSame(0, count($data));
    }

    public function testSearchInTitleOrder(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title3', 'image1', Music::TYPE_TRACK, false, time(), time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'title1', 'image2', Music::TYPE_TRACK, false, time(), time()],
            ['url3', Music::PROVIDER_SOUNDCLOUD, 'key3', 'title2', 'image3', Music::TYPE_TRACK, false, time(), time()],
            ['url3', Music::PROVIDER_SOUNDCLOUD, 'key3', 'foo', 'image3', Music::TYPE_TRACK, false, time(), time()],
        ]);

        $data = Track::find()->searchInTitleOrder('title')->all();
        $this->assertSame(3, count($data));
        $this->assertSame('title1', $data[0]->title);
        $this->assertSame('title2', $data[1]->title);
        $this->assertSame('title3', $data[2]->title);
    }

    public function testFavoritesLatest(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, true, time() + 3, time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'title2', 'image2', Music::TYPE_TRACK, false, time(), time()],
            ['url3', Music::PROVIDER_BANDCAMP, 'key3', 'title3', 'image3', Music::TYPE_TRACK, true, time() + 5, time()],
            ['url4', Music::PROVIDER_BANDCAMP, 'key4', 'title4', 'image4', Music::TYPE_TRACK, false, time(), time()],
            ['url5', Music::PROVIDER_BANDCAMP, 'key5', 'title5', 'image5', Music::TYPE_TRACK, true, time() + 1, time()],
        ]);

        $data = Track::find()->favoritesInLatestOrder()->all();
        $this->assertSame(3, count($data));
        $this->assertSame('title3', $data[0]->title);
        $this->assertSame('title1', $data[1]->title);
        $this->assertSame('title5', $data[2]->title);
    }
}
