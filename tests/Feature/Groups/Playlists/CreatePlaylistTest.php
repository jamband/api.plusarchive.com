<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Playlists;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Playlists\Playlist;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Jamband\Ripple\Ripple;
use Mockery\MockInterface;
use Tests\TestCase;

class CreatePlaylistTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private MusicProviderFactory $providerFactory;
    private Playlist $playlist;
    private Ripple $ripple;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->providerFactory = new MusicProviderFactory();
        $this->playlist = new Playlist();
        $this->ripple = $this->app->make(Ripple::class);
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/playlists')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/playlists')
            ->assertUnauthorized();
    }

    public function testCreatePlaylist(): void
    {
        $provider = $this->providerFactory
            ->createOne();

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($provider) {
            $mock->shouldReceive('image')->andReturn('image1');
            $mock->shouldReceive('provider')->andReturn($provider->name);
            $mock->shouldReceive('id')->andReturn('key1');
        });

        $this->assertDatabaseCount($this->playlist::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/playlists', [
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

        $this->assertDatabaseCount($this->playlist::class, 1)
            ->assertDatabaseHas($this->playlist::class, [
                'id' => 1,
                'url' => 'https://soundcloud.com/foo/sets/bar',
                'provider_id' => $provider->id,
                'provider_key' => 'key1',
                'title' => 'playlist1',
            ]);
    }

    public function testCreatePlaylistWithSomeEmptyAttributeValues(): void
    {
        $provider = $this->providerFactory
            ->createOne();

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($provider) {
            $mock->shouldReceive('image')->andReturn('image1');
            $mock->shouldReceive('title')->andReturn('playlist1');
            $mock->shouldReceive('provider')->andReturn($provider->name);
            $mock->shouldReceive('id')->andReturn('key1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->post('/playlists', [
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

        $this->assertDatabaseHas($this->playlist::class, [
            'id' => 1,
            'url' => 'https://soundcloud.com/foo/sets/bar',
            'provider_id' => $provider->id,
            'provider_key' => 'key1',
            'title' => 'playlist1',
        ]);
    }
}
