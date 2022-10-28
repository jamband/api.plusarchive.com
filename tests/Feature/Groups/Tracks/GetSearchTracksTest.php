<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetSearchTracksTest extends TestCase
{
    use RefreshDatabase;

    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testGetSearchTracks(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = TrackFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['title' => 'foo'],
                ['title' => 'bar'],
                ['title' => 'baz'],
            ))
            ->hasAttached(
                factory: TrackGenreFactory::new()
                    ->count(2),
                relationship: 'genres',
            )
            ->create();

        $this->getJson('/tracks/search?q=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tracks) {
                $json->where('data.0', [
                    'id' => $this->hashids->encode($tracks[1]->id),
                    'url' => $tracks[1]->url,
                    'provider' => $tracks[1]->provider->name,
                    'provider_key' => $tracks[1]->provider_key,
                    'title' => $tracks[1]->title,
                    'image' => $tracks[1]->image,
                    'genres' => [
                        $tracks[1]->genres[0]->name,
                        $tracks[1]->genres[1]->name,
                    ],
                    'created_at' => $tracks[1]->created_at->format('Y.m.d'),
                ]);

                $json->where('data.1', [
                    'id' => $this->hashids->encode($tracks[2]->id),
                    'url' => $tracks[2]->url,
                    'provider' => $tracks[2]->provider->name,
                    'provider_key' => $tracks[2]->provider_key,
                    'title' => $tracks[2]->title,
                    'image' => $tracks[2]->image,
                    'genres' => [
                        $tracks[2]->genres[0]->name,
                        $tracks[2]->genres[1]->name,
                    ],
                    'created_at' => $tracks[2]->created_at->format('Y.m.d'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                   ->where('currentPage', 1)
                   ->where('lastPage', 1)
                   ->where('perPage', 24)
                   ->where('total', 2));
            });
    }

    public function testGetSearchTracksWithoutParameter(): void
    {
        TrackFactory::new()
            ->createOne();

        $this->getJson('/tracks/search')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetSearchTracksWithUnmatchedSearch(): void
    {
        TrackFactory::new()
            ->state(['title' => 'foo'])
            ->createOne();

        $this->getJson('/tracks/search?q=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testQueryStringTypes(): void
    {
        $this->getJson('/tracks/search?q[]=')
            ->assertOk();
    }
}
