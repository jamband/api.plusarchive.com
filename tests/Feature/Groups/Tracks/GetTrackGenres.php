<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenreFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetTrackGenres extends TestCase
{
    use RefreshDatabase;

    private TrackGenreFactory $genreFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genreFactory = new TrackGenreFactory();
    }

    public function testGetTrackProviders(): void
    {
        $this->genreFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->get('/tracks/genres')
            ->assertOk()
            ->assertExactJson(['bar', 'baz', 'foo']);
    }
}
