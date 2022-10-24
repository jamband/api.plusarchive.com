<?php

declare(strict_types=1);

namespace Tests\Feature\Playlists;

use App\Groups\Playlists\Playlist;
use App\Groups\Playlists\PlaylistFactory;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetPlaylistsTest extends TestCase
{
    use RefreshDatabase;

    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testGetPlaylists(): void
    {
        /** @var array<int, Playlist> $playlists */
        $playlists = PlaylistFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->getJson('/playlists')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJson(function (AssertableJson $json) use ($playlists) {
                $json->where('0', [
                    'id' => $this->hashids->encode($playlists[1]->id),
                    'url' => $playlists[1]->url,
                    'provider' => $playlists[1]->provider->name,
                    'provider_key' => $playlists[1]->provider_key,
                    'title' => $playlists[1]->title,
                ]);

                $json->where('1', [
                    'id' => $this->hashids->encode($playlists[0]->id),
                    'url' => $playlists[0]->url,
                    'provider' => $playlists[0]->provider->name,
                    'provider_key' => $playlists[0]->provider_key,
                    'title' => $playlists[0]->title,
                ]);
            });
    }
}
