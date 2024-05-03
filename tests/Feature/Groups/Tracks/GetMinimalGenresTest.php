<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetMinimalGenresTest extends TestCase
{
    use RefreshDatabase;

    private TrackFactory $trackFactory;
    private TrackGenreFactory $genreFactory;
    private TrackGenre $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trackFactory = new TrackFactory();
        $this->genreFactory = new TrackGenreFactory();
        $this->genre = new TrackGenre();
    }

    public function testGetMinimalGenres(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(5)
            ->create();

        $this->genreFactory
            ->count(5)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'genre'.($sequence->index + 1),
            ]))
            ->create();

        $tracks[0]->genres()->sync([1]);
        $tracks[1]->genres()->sync([1]);
        $tracks[2]->genres()->sync([3]);
        $tracks[3]->genres()->sync([3, 5]);
        $tracks[4]->genres()->sync([2, 4, 5]);

        $this->get('/tracks/minimal-genres?limit=3')
            ->assertOk()
            ->assertExactJson([
                'genre1',
                'genre3',
                'genre5',
            ]);
    }

    public function testGetMinimalGenresWithoutLimitParameter(): void
    {
        $this->trackFactory
            ->count(5)
            ->hasAttached(
                factory: $this->genreFactory
                    ->count(3),
                relationship: 'genres',
            )
            ->create();

        $this->assertDatabaseCount($this->genre::class, 15);

        $this->get('/tracks/minimal-genres')
            ->assertOk()
            ->assertJsonCount(10);
    }

    public function testQueryStringTypes(): void
    {
        $this->get('/tracks/minimal-genres?limit[]=')
            ->assertOk();
    }
}
