<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTrackTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackFactory $trackFactory;
    private TrackGenreFactory $genreFactory;
    private TrackGenre $genre;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->trackFactory = new TrackFactory();
        $this->genreFactory = new TrackGenreFactory();
        $this->genre = new TrackGenre();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/tracks/'.str_repeat('a', 11))
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/tracks/'.str_repeat('a', 11))
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->delete('/tracks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/tracks/'.$this->hashids->encode(1))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testNotFoundWithInvalidHashValue(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/tracks/'.str_repeat('a', 11))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testDeleteTrack(): void
    {
        $track = $this->trackFactory
            ->createOne();

        $this->assertDatabaseCount($track::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/tracks/'.$this->hashids->encode($track->id))
            ->assertNoContent();

        $this->assertDatabaseCount($track::class, 0);
    }

    public function testDeleteTrackWithGenres(): void
    {
        $track = $this->trackFactory
            ->createOne();

        $pivotTable = $track->genres()->getTable();

        $this->genreFactory
            ->count(2)
            ->create();

        $track->genres()->sync([1, 2]);

        $this->assertDatabaseCount($track::class, 1)
            ->assertDatabaseCount($this->genre::class, 2)
            ->assertDatabaseCount($pivotTable, 2);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/tracks/'.$this->hashids->encode($track->id))
            ->assertNoContent();

        $this->assertDatabaseCount($track::class, 0)
            ->assertDatabaseCount($this->genre::class, 2)
            ->assertDatabaseCount($pivotTable, 0);
    }
}
