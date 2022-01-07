<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\MusicGenre;
use app\tests\Database;
use app\tests\TestCase;
use app\tests\unit\fixtures\music\MusicGenreFixture;
use app\tests\unit\fixtures\music\MusicGenreMinimalFixture;

/** @see MusicGenre */
class MusicGenreTest extends TestCase
{
    public function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(MusicGenre::tableName());
    }

    public function fixtures(): array
    {
        return [
            'genre' => MusicGenreFixture::class,
            'genreMinimal' => MusicGenreMinimalFixture::class,

        ];
    }

    public function testTableName(): void
    {
        $this->assertSame('music_genre', MusicGenre::tableName());
    }

    public function testFields(): void
    {
        /** @var MusicGenreFixture $fixture */
        $fixture = $this->getFixture('genre');
        $fixture->load();
        $genre1Fixture = $fixture->data['genre1'];

        $data = MusicGenre::findOne(1)->toArray();
        $this->assertCount(1, $data);
        $this->assertSame($genre1Fixture['name'], $data['name']);
    }

    public function testMinimal(): void
    {
        /** @var MusicGenreMinimalFixture $fixture */
        $fixture = $this->getFixture('genreMinimal');
        $fixture->load();

        $data = MusicGenre::minimal(3);
        $this->assertSame($fixture->data['genre1']['name'], $data[0]);
        $this->assertSame($fixture->data['genre2']['name'], $data[1]);
        $this->assertSame($fixture->data['genre5']['name'], $data[2]);
    }

    public function testTrait(): void
    {
        $genre = new MusicGenre;
        $this->assertTrue($genre->hasMethod('names'));
        $this->assertTrue($genre->hasMethod('countries'));
    }
}
