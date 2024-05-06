<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetTracksTest extends TestCase
{
    use RefreshDatabase;

    private TrackFactory $trackFactory;
    private TrackGenreFactory $genreFactory;
    private MusicProviderFactory $providerFactory;
    private Carbon $carbon;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trackFactory = new TrackFactory();
        $this->genreFactory = new TrackGenreFactory();
        $this->providerFactory = new MusicProviderFactory();
        $this->carbon = new Carbon();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testGetTracks(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(2)
            ->hasAttached(
                factory: $this->genreFactory
                    ->count(2),
                relationship: 'genres',
            )
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon)->addMinutes($sequence->index),
            ]))
            ->create();

        $this->get('/tracks')
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
                    'id' => $this->hashids->encode($tracks[0]->id),
                    'url' => $tracks[0]->url,
                    'provider' => $tracks[0]->provider->name,
                    'provider_key' => $tracks[0]->provider_key,
                    'title' => $tracks[0]->title,
                    'image' => $tracks[0]->image,
                    'genres' => [
                        $tracks[0]->genres[0]->name,
                        $tracks[0]->genres[1]->name,
                    ],
                    'created_at' => $tracks[0]->created_at->format('Y.m.d'),
                ]);

                $json->where('pagination', [
                    'currentPage' => 1,
                    'lastPage' => 1,
                    'perPage' => 24,
                    'total' => 2,
                ]);
            });
    }

    public function testGetTracksWithProvider(): void
    {
        $this->trackFactory
            ->for(
                $this->providerFactory
                    ->state(['name' => 'foo']),
                'provider',
            )
            ->createOne();

        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(2)
            ->for(
                $this->providerFactory
                    ->state(['name' => 'bar']),
                'provider',
            )
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon)->addMinutes($sequence->index),
            ]))
            ->create();

        $this->get('/tracks?provider=bar')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tracks) {
                $json->where('data.0', [
                    'id' => $this->hashids->encode($tracks[1]->id),
                    'url' => $tracks[1]->url,
                    'provider' => 'bar',
                    'provider_key' => $tracks[1]->provider_key,
                    'title' => $tracks[1]->title,
                    'image' => $tracks[1]->image,
                    'genres' => [],
                    'created_at' => $tracks[1]->created_at->format('Y.m.d'),
                ]);

                $json->where('data.1', [
                    'id' => $this->hashids->encode($tracks[0]->id),
                    'url' => $tracks[0]->url,
                    'provider' => 'bar',
                    'provider_key' => $tracks[0]->provider_key,
                    'title' => $tracks[0]->title,
                    'image' => $tracks[0]->image,
                    'genres' => [],
                    'created_at' => $tracks[0]->created_at->format('Y.m.d'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetTracksWithUnmatchedProvider(): void
    {
        $this->trackFactory
            ->for(
                $this->providerFactory
                    ->state(['name' => 'foo']),
                'provider',
            )
            ->createOne();

        $this->get('/tracks?provider=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetTracksWithGenre(): void
    {
        $this->trackFactory
            ->hasAttached(
                factory: $this->genreFactory
                    ->state(['name' => 'foo']),
                relationship: 'genres',
            )
            ->createOne();

        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon)->addMinutes($sequence->index),
            ]))
            ->create();

        $this->genreFactory
            ->state(['name' => 'bar'])
            ->createOne();

        $tracks[0]->genres()->sync([2]);
        $tracks[1]->genres()->sync([2]);

        $this->get('/tracks?genre=bar')
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
                    'genres' => ['bar'],
                    'created_at' => $tracks[1]->created_at->format('Y.m.d'),
                ]);

                $json->where('data.1', [
                    'id' => $this->hashids->encode($tracks[0]->id),
                    'url' => $tracks[0]->url,
                    'provider' => $tracks[0]->provider->name,
                    'provider_key' => $tracks[0]->provider_key,
                    'title' => $tracks[0]->title,
                    'image' => $tracks[0]->image,
                    'genres' => ['bar'],
                    'created_at' => $tracks[0]->created_at->format('Y.m.d'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetTracksWithUnmatchedGenre(): void
    {
        $this->trackFactory
            ->hasAttached(
                factory: $this->genreFactory
                    ->state(['name' => 'foo']),
                relationship: 'genres',
            )
            ->createOne();

        $this->get('/tracks?genre=bar')
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->where('data', [])
                    ->has('pagination', fn (AssertableJson $json) => $json
                        ->where('total', 0)
                        ->etc());
            });
    }

    public function testGetTracksWithProviderAndGenre(): void
    {
        $this->providerFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'provider'.($sequence->index + 1),
            ]))
            ->create();

        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(4)
            ->state(new Sequence(
                ['provider_id' => 1],
                ['provider_id' => 1],
                ['provider_id' => 1],
                ['provider_id' => 2],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon)->addMinutes($sequence->index),
            ]))
            ->create();

        $this->genreFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'genre'.($sequence->index + 1),
            ]))
            ->create();

        $tracks[0]->genres()->sync([1]);
        $tracks[1]->genres()->sync([1]);

        $this->get('/tracks?provider=provider1&genre=genre1')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tracks) {
                $json->where('data.0', [
                    'id' => $this->hashids->encode($tracks[1]->id),
                    'url' => $tracks[1]->url,
                    'provider' => 'provider1',
                    'provider_key' => $tracks[1]->provider_key,
                    'title' => $tracks[1]->title,
                    'image' => $tracks[1]->image,
                    'genres' => ['genre1'],
                    'created_at' => $tracks[1]->created_at->format('Y.m.d'),
                ]);

                $json->where('data.1', [
                    'id' => $this->hashids->encode($tracks[0]->id),
                    'url' => $tracks[0]->url,
                    'provider' => 'provider1',
                    'provider_key' => $tracks[0]->provider_key,
                    'title' => $tracks[0]->title,
                    'image' => $tracks[0]->image,
                    'genres' => ['genre1'],
                    'created_at' => $tracks[1]->created_at->format('Y.m.d'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->get('/tracks?provider[]=&genre[]=')
            ->assertOk();
    }
}
