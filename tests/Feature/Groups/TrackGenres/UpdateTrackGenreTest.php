<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\TrackGenres;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateTrackGenreTest extends TestCase
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
            ->put('/track-genres/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/track-genres/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->put('/track-genres/1', [
                'name' => 'updated_genre1',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateGenre(): void
    {
        $genre = $this->genreFactory
            ->createOne();

        $this->assertDatabaseCount($genre::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/track-genres/'.$genre->id, [
                'name' => 'updated_genre1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($genre) {
                $json->where('id', $genre->id)
                    ->where('name', 'updated_genre1');
            });

        $this->assertDatabaseCount($genre::class, 1)
            ->assertDatabaseHas($genre::class, [
                'id' => $genre->id,
                'name' => 'updated_genre1',
            ]);
    }
}
