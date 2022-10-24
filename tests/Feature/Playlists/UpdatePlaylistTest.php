<?php

declare(strict_types=1);

namespace Tests\Feature\Playlists;

use App\Groups\Playlists\Playlist;
use App\Groups\Playlists\PlaylistFactory;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Jamband\Ripple\Ripple;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdatePlaylistTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /playlists/'.str_repeat('a', 11));
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /playlists/'.str_repeat('a', 11));
    }

    public function testNotFound(): void
    {
        $this->putJson('/playlists/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testModelNotFound(): void
    {
        $this->partialMock(Ripple::class, function (MockInterface $mock) {
            $mock->shouldReceive('id')->andReturn('updated_key1');
            $mock->shouldReceive('image')->andReturn('updated-image1');
        });

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/playlists/'.$this->hashids->encode(1), [
                'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
                'title' => 'updated_title1',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testModelNotFoundWithInvalidHashValue(): void
    {
        $this->partialMock(Ripple::class, function (MockInterface $mock) {
            $mock->shouldReceive('id')->andReturn('updated_key1');
            $mock->shouldReceive('image')->andReturn('updated-image1');
        });

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/playlists/'.str_repeat('a', 11), [
                'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
                'title' => 'updated_title1',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdatePlaylist(): void
    {
        $playlist = PlaylistFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Playlist::class, 1);

        $this->partialMock(Ripple::class, function (MockInterface $mock) use ($playlist) {
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('provider')->andReturn($playlist->provider->name);
            $mock->shouldReceive('id')->andReturn('updated_key1');
        });

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/playlists/'.$this->hashids->encode($playlist->id), [
                'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
                'title' => 'updated_playlist1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($playlist) {
                $json->where('id', $this->hashids->encode($playlist->id))
                    ->where('url', 'https://soundcloud.com/updated-foo/sets/updated-bar')
                    ->where('provider', $playlist->provider->name)
                    ->where('title', 'updated_playlist1')
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(Playlist::class, 1);

        $this->assertDatabaseHas(Playlist::class, [
            'id' => $playlist->id,
            'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
            'provider_id' => $playlist->provider_id,
            'provider_key' => 'updated_key1',
            'title' => 'updated_playlist1',
        ]);
    }

    public function testUpdatePlaylistWithSomeEmptyAttributeValues(): void
    {
        $playlist = PlaylistFactory::new()
            ->createOne();

        $this->partialMock(Ripple::class, function (MockInterface $mock) use ($playlist) {
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('title')->andReturn('updated_playlist1');
            $mock->shouldReceive('provider')->andReturn($playlist->provider->name);
            $mock->shouldReceive('id')->andReturn('updated_key1');
        });

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/playlists/'.$this->hashids->encode($playlist->id), [
                'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($playlist) {
                $json->where('id', $this->hashids->encode($playlist->id))
                ->where('url', 'https://soundcloud.com/updated-foo/sets/updated-bar')
                ->where('provider', $playlist->provider->name)
                ->where('title', 'updated_playlist1')
                ->has('created_at')
                ->has('updated_at');
            });

        $this->assertDatabaseHas(Playlist::class, [
            'id' => $playlist->id,
            'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
            'provider_id' => $playlist->provider_id,
            'provider_key' => 'updated_key1',
            'title' => 'updated_playlist1',
        ]);
    }
}
