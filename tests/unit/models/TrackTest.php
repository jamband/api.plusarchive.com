<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Music;
use app\models\MusicGenre;
use app\models\Track;
use app\queries\TrackQuery;
use app\tests\Database;
use app\tests\unit\fixtures\music\TrackFixture;
use app\tests\unit\fixtures\music\TrackGenreAssnFixture;
use app\tests\unit\fixtures\music\TrackGenreFixture;
use creocoder\taggable\TaggableBehavior;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\test\FixtureTrait;

/** @see Track */
class TrackTest extends TestCase
{
    use FixtureTrait;

    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(Music::tableName());
        $this->db->createTable(MusicGenre::tableName());
        $this->db->createTable(MusicGenre::tableName().'_assn');
    }

    public function fixtures(): array
    {
        return [
            'track' => TrackFixture::class,
            'genre' => TrackGenreFixture::class,
            'genreAssn' => TrackGenreAssnFixture::class,
        ];
    }

    public function testFields(): void
    {
        /** @var TrackFixture $fixture */
        $fixture = $this->getFixture('track');
        $fixture->load();
        $track1Fixture = $fixture->data['track1'];

        $data = Track::findOne(1)->toArray();
        $this->assertCount(7, $data);
        $this->assertMatchesRegularExpression('/[\w-]{11}/', $data['id']);
        $this->assertSame($track1Fixture['url'], $data['url']);
        $this->assertSame(Music::PROVIDERS[$track1Fixture['provider']], $data['provider']);
        $this->assertSame($track1Fixture['provider_key'], $data['provider_key']);
        $this->assertSame($track1Fixture['title'], $data['title']);
        $this->assertSame($track1Fixture['image'], $data['image']);

        $format = fn(int $value): string => Yii::$app->formatter->asDate($value);
        $this->assertSame($format($track1Fixture['created_at']), $data['created_at']);
    }

    public function testFind(): void
    {
        $query = Track::find();
        $this->assertInstanceOf(TrackQuery::class, $query);
    }

    public function testGetGenres(): void
    {
        $this->getFixture('track')->load();

        /** @var TrackGenreFixture $fixture */
        $fixture = $this->getFixture('genre');
        $fixture->load();
        $genre1Fixture = $fixture->data['genre1'];

        $this->getFixture('genreAssn')->load();

        $data = Track::find()->all();
        $this->assertSame($genre1Fixture['name'], $data[0]->genres[0]->name);
    }

    public function testBehaviors(): void
    {
        $track = new Track;
        $this->assertArrayHasKey('taggable', $track->behaviors);
        $this->assertInstanceOf(TaggableBehavior::class, $track->behaviors['taggable']);
    }
}
