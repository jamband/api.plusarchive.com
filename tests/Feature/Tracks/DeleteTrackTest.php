<?php

declare(strict_types=1);

namespace Tests\Feature\Tracks;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteTrackTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware(
            'DELETE /tracks/'.str_repeat('a', 11)
        );
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware(
            'DELETE /tracks/'.str_repeat('a', 11)
        );
    }

    public function testNotFound(): void
    {
        $this->deleteJson('/tracks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/tracks/'.$this->hashids->encode(1))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testModelNotFoundWithInvalidHashValue(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/tracks/'.str_repeat('a', 11))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteTrack(): void
    {
        $track = TrackFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Track::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/tracks/'.$this->hashids->encode($track->id))
            ->assertNoContent();

        $this->assertDatabaseCount(Track::class, 0);
    }

    public function testDeleteTrackWithGenres(): void
    {
        $track = TrackFactory::new()
            ->createOne();

        $pivotTable = $track->genres()->getTable();

        TrackGenreFactory::new()
            ->count(2)
            ->create();

        $track->genres()->sync([1, 2]);

        $this->assertDatabaseCount(Track::class, 1);
        $this->assertDatabaseCount(TrackGenre::class, 2);
        $this->assertDatabaseCount($pivotTable, 2);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/tracks/'.$this->hashids->encode($track->id))
            ->assertNoContent();

        $this->assertDatabaseCount(Track::class, 0);
        $this->assertDatabaseCount(TrackGenre::class, 2);
        $this->assertDatabaseCount($pivotTable, 0);
    }
}
