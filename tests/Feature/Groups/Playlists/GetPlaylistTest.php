<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Playlists;

use App\Groups\Playlists\PlaylistFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetPlaylistTest extends TestCase
{
    use RefreshDatabase;

    private PlaylistFactory $playlistFactory;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playlistFactory = new PlaylistFactory();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testNotFound(): void
    {
        $this->get('/playlists/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testModelNotFound(): void
    {
        $this->get('/playlists/'.$this->hashids->encode(1))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testModelNotFoundWithInvalidHashValue(): void
    {
        $this->get('/playlists/'.str_repeat('a', 11))
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetPlaylist(): void
    {
        $playlist = $this->playlistFactory
            ->createOne();

        $this->get('/playlists/'.$this->hashids->encode($playlist->id))
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($playlist) {
                $json->where('id', $this->hashids->encode($playlist->id))
                    ->where('url', $playlist->url)
                    ->where('provider', $playlist->provider->name)
                    ->where('provider_key', $playlist->provider_key)
                    ->where('title', $playlist->title);
            });
    }
}
