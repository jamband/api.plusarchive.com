<?php

declare(strict_types=1);

namespace Tests\Feature\TrackGenres;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteTrackGenreTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /track-genres/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /track-genres/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/track-genres/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteGenre(): void
    {
        $genre = TrackGenreFactory::new()
            ->createOne();

        $this->assertDatabaseCount(TrackGenre::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/track-genres/'.$genre->id)
            ->assertNoContent();

        $this->assertDatabaseCount(TrackGenre::class, 0);
    }
}
