<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\TrackGenres;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateTrackGenreTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackGenre $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->genre = new TrackGenre();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/track-genres')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/track-genres')
            ->assertUnauthorized();
    }

    public function testCreateGenre(): void
    {
        $this->assertDatabaseCount($this->genre::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/track-genres', [
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

        $this->assertDatabaseCount($this->genre::class, 1)
            ->assertDatabaseHas($this->genre::class, [
                'id' => 1,
                'name' => 'genre1',
            ]);
    }
}
