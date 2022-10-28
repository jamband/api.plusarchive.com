<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetFavoriteTracksTest extends TestCase
{
    use RefreshDatabase;

    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testGetFavoriteTracks(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = TrackFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['urge' => false],
                ['urge' => true],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->hasAttached(
                factory: TrackGenreFactory::new()
                    ->count(2),
                relationship: 'genres',
            )
            ->create();

        $this->getJson('/tracks/favorites')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJson(function (AssertableJson $json) use ($tracks) {
                $json->where('0', [
                    'id' => $this->hashids->encode($tracks[3]->id),
                    'url' => $tracks[3]->url,
                    'provider' => $tracks[3]->provider->name,
                    'provider_key' => $tracks[3]->provider_key,
                    'title' => $tracks[3]->title,
                    'image' => $tracks[3]->image,
                    'genres' => [
                        $tracks[3]->genres[0]->name,
                        $tracks[3]->genres[1]->name,
                    ],
                    'created_at' => $tracks[3]->created_at->format('Y.m.d'),
                ]);

                $json->where('1', [
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
            });
    }
}
