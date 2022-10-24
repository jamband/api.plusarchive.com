<?php

declare(strict_types=1);

namespace Tests\Feature\Playlists;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Playlists\Playlist;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Jamband\Ripple\Ripple;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreatePlaylistTest extends TestCase
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
        $this->assertVerifiedMiddleware('POST /playlists');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /playlists');
    }

    public function testCreatePlaylist(): void
    {
        $provider = MusicProviderFactory::new()
            ->createOne();

        $this->partialMock(Ripple::class, function (MockInterface $mock) use ($provider) {
            $mock->shouldReceive('image')->andReturn('image1');
            $mock->shouldReceive('provider')->andReturn($provider->name);
            $mock->shouldReceive('id')->andReturn('key1');
        });

        $this->assertDatabaseCount(Playlist::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/playlists', [
                'url' => 'https://soundcloud.com/foo/sets/bar',
                'title' => 'playlist1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/playlists/1'
            )
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $this->hashids->encode(1))
                    ->where('url', 'https://soundcloud.com/foo/sets/bar')
                    ->where('provider', $provider->name)
                    ->where('title', 'playlist1')
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(Playlist::class, 1);

        $this->assertDatabaseHas(Playlist::class, [
            'id' => 1,
            'url' => 'https://soundcloud.com/foo/sets/bar',
            'provider_id' => $provider->id,
            'provider_key' => 'key1',
            'title' => 'playlist1',
        ]);
    }

    public function testCreatePlaylistWithSomeEmptyAttributeValues(): void
    {
        $provider = MusicProviderFactory::new()
            ->createOne();

        $this->partialMock(Ripple::class, function (MockInterface $mock) use ($provider) {
            $mock->shouldReceive('image')->andReturn('image1');
            $mock->shouldReceive('title')->andReturn('playlist1');
            $mock->shouldReceive('provider')->andReturn($provider->name);
            $mock->shouldReceive('id')->andReturn('key1');
        });

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/playlists', [
                'url' => 'https://soundcloud.com/foo/sets/bar',
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $this->hashids->encode(1))
                    ->where('url', 'https://soundcloud.com/foo/sets/bar')
                    ->where('provider', $provider->name)
                    ->where('title', 'playlist1')
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas(Playlist::class, [
            'id' => 1,
            'url' => 'https://soundcloud.com/foo/sets/bar',
            'provider_id' => $provider->id,
            'provider_key' => 'key1',
            'title' => 'playlist1',
        ]);
    }
}
