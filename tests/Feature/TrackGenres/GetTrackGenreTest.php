<?php

declare(strict_types=1);

namespace Tests\Feature\TrackGenres;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetTrackGenreTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /track-genres/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /track-genres/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/track-genres/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetGenre(): void
    {
        $genre = TrackGenreFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/track-genres/1')
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($genre) {
                $json->where('id', $genre->id)
                    ->where('name', $genre->name);
            });
    }
}
