<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetTrackTest extends TestCase
{
    use RefreshDatabase;

    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testNotFound(): void
    {
        $this->getJson('/tracks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testModelNotFound(): void
    {
        $this->getJson('/tracks/'.$this->hashids->encode(1))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testModelNotFoundWithInvalidHashValue(): void
    {
        $this->getJson('/tracks/'.str_repeat('a', 11))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetTrack(): void
    {
        /** @var Track $track */
        $track = TrackFactory::new()
            ->hasAttached(
                factory: TrackGenreFactory::new()
                    ->count(2),
                relationship: 'genres',
            )
            ->createOne();

        $this->getJson('/tracks/'.$this->hashids->encode(1))
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($track) {
                $json->where('id', $this->hashids->encode(1))
                    ->where('url', $track->url)
                    ->where('provider', $track->provider->name)
                    ->where('provider_key', $track->provider_key)
                    ->where('title', $track->title)
                    ->where('image', $track->image)
                    ->where('genres', [
                        $track->genres[0]->name,
                        $track->genres[1]->name,
                    ])
                    ->where('created_at', $track->created_at->format('Y.m.d'));
            });
    }
}
