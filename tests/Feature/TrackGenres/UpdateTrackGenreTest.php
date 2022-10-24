<?php

declare(strict_types=1);

namespace Tests\Feature\TrackGenres;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateTrackGenreTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /track-genres/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /track-genres/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/track-genres/1', [
                'name' => 'updated_genre1',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateGenre(): void
    {
        $genre = TrackGenreFactory::new()
            ->createOne();

        $this->assertDatabaseCount(TrackGenre::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/track-genres/'.$genre->id, [
                'name' => 'updated_genre1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($genre) {
                $json->where('id', $genre->id)
                    ->where('name', 'updated_genre1');
            });

        $this->assertDatabaseCount(TrackGenre::class, 1);

        $this->assertDatabaseHas(TrackGenre::class, [
            'id' => $genre->id,
            'name' => 'updated_genre1',
        ]);
    }
}
