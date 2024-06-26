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

    private TrackFactory $trackFactory;
    private TrackGenreFactory $genreFactory;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trackFactory = new TrackFactory();
        $this->genreFactory = new TrackGenreFactory();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testNotFound(): void
    {
        $this->get('/tracks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);

        $this->get('/tracks/'.$this->hashids->encode(1))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testNotFoundWithInvalidHashValue(): void
    {
        $this->get('/tracks/'.str_repeat('a', 11))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testGetTrack(): void
    {
        /** @var Track $track */
        $track = $this->trackFactory
            ->hasAttached(
                factory: $this->genreFactory
                    ->count(2),
                relationship: 'genres',
            )
            ->createOne();

        $this->get('/tracks/'.$this->hashids->encode(1))
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
