<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\TrackGenres;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTrackGenreTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackGenreFactory $genreFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->genreFactory = new TrackGenreFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/track-genres/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/track-genres/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->delete('/track-genres/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteGenre(): void
    {
        $genre = $this->genreFactory
            ->createOne();

        $this->assertDatabaseCount($genre::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/track-genres/'.$genre->id)
            ->assertNoContent();

        $this->assertDatabaseCount($genre::class, 0);
    }
}
