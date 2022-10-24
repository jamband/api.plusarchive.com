<?php

declare(strict_types=1);

namespace Tests\Feature\TrackGenres;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateTrackGenreTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /track-genres');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /track-genres');
    }

    public function testCreateGenre(): void
    {
        $this->assertDatabaseCount(TrackGenre::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/track-genres', [
                'name' => 'genre1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/genres/1'
            )
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', 1)
                ->where('name', 'genre1'));

        $this->assertDatabaseCount(TrackGenre::class, 1);

        $this->assertDatabaseHas(TrackGenre::class, [
            'id' => 1,
            'name' => 'genre1',
        ]);
    }
}
