<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Playlists;

use App\Groups\Playlists\PlaylistFactory;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Jamband\Ripple\Ripple;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdatePlaylistTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private PlaylistFactory $playlistFactory;
    private Ripple $ripple;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->playlistFactory = new PlaylistFactory();
        $this->ripple = new Ripple();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->put('/playlists/'.str_repeat('a', 11))
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/playlists/'.str_repeat('a', 11))
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->put('/playlists/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);

        $this->partialMock($this->ripple::class, function (MockInterface $mock) {
            $mock->shouldReceive('id')->andReturn('updated_key1');
            $mock->shouldReceive('image')->andReturn('updated-image1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/playlists/'.$this->hashids->encode(1), [
                'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
                'title' => 'updated_title1',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testNotFoundWithInvalidHashValue(): void
    {
        $this->partialMock($this->ripple::class, function (MockInterface $mock) {
            $mock->shouldReceive('id')->andReturn('updated_key1');
            $mock->shouldReceive('image')->andReturn('updated-image1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/playlists/'.str_repeat('a', 11), [
                'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
                'title' => 'updated_title1',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testUpdatePlaylist(): void
    {
        $playlist = $this->playlistFactory
            ->createOne();

        $this->assertDatabaseCount($playlist::class, 1);

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($playlist) {
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('provider')->andReturn($playlist->provider->name);
            $mock->shouldReceive('id')->andReturn('updated_key1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/playlists/'.$this->hashids->encode($playlist->id), [
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

        $this->assertDatabaseCount($playlist::class, 1)
            ->assertDatabaseHas($playlist::class, [
                'id' => $playlist->id,
                'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
                'provider_id' => $playlist->provider_id,
                'provider_key' => 'updated_key1',
                'title' => 'updated_playlist1',
            ]);
    }

    public function testUpdatePlaylistWithSomeEmptyAttributeValues(): void
    {
        $playlist = $this->playlistFactory
            ->createOne();

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($playlist) {
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('title')->andReturn('updated_playlist1');
            $mock->shouldReceive('provider')->andReturn($playlist->provider->name);
            $mock->shouldReceive('id')->andReturn('updated_key1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/playlists/'.$this->hashids->encode($playlist->id), [
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

        $this->assertDatabaseHas($playlist::class, [
            'id' => $playlist->id,
            'url' => 'https://soundcloud.com/updated-foo/sets/updated-bar',
            'provider_id' => $playlist->provider_id,
            'provider_key' => 'updated_key1',
            'title' => 'updated_playlist1',
        ]);
    }
}
