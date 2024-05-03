<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Playlists;

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

    private PlaylistFactory $playlistFactory;
    private Carbon $carbon;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playlistFactory = new PlaylistFactory();
        $this->carbon = new Carbon();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testGetPlaylists(): void
    {
        /** @var array<int, Playlist> $playlists */
        $playlists = $this->playlistFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->get('/playlists')
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
