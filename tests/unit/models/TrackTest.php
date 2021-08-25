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

namespace app\tests\unit\models;

use app\models\Music;
use app\models\Track;
use app\queries\TrackQuery;
use app\tests\Database;
use app\tests\TestCase;
use creocoder\taggable\TaggableBehavior;

class TrackTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('music');
        $this->db->createTable('music_genre');
        $this->db->createTable('music_genre_assn');

        parent::setUp();
    }

    public function testFields(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time(), time()],
        ]);

        $data = Track::findOne(1)->toArray();
        $this->assertMatchesRegularExpression('/[\w-]{11}/', $data['id']);
        $this->assertSame('url1', $data['url']);
        $this->assertSame('Bandcamp', $data['provider']);
        $this->assertSame('key1', $data['provider_key']);
        $this->assertSame('title1', $data['title']);
        $this->assertSame('image1', $data['image']);
        $this->assertArrayNotHasKey('type', $data);
        $this->assertArrayNotHasKey('urge', $data);
        $this->assertMatchesRegularExpression('/\A[0-9]{4}\.[0-9]{2}\.[0-9]{2}\z/', $data['created_at']);
        $this->assertArrayNotHasKey('updated_at', $data);
    }

    public function testFind(): void
    {
        $query = Track::find();
        $this->assertInstanceOf(TrackQuery::class, $query);
    }

    public function testGetGenres(): void
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

        $data = Track::find()->all();
        $this->assertSame('genre1', $data[0]->genres[0]->name);
    }

    public function testBehaviors(): void
    {
        $track = new Track;
        $this->assertArrayHasKey('taggable', $track->behaviors);
        $this->assertInstanceOf(TaggableBehavior::class, $track->behaviors['taggable']);
    }
}
