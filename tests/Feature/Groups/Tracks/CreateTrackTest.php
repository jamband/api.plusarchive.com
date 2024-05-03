<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\TrackGenres\TrackGenre;
use App\Groups\Tracks\Track;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as Client;
use Illuminate\Testing\Fluent\AssertableJson;
use Jamband\Ripple\Ripple;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateTrackTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private MusicProviderFactory $providerFactory;
    private Track $track;
    private TrackGenre $genre;
    private Ripple $ripple;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->providerFactory = new MusicProviderFactory();
        $this->track = new Track();
        $this->genre = new TrackGenre();
        $this->ripple = $this->app->make(Ripple::class);

        $client = $this->app->make(Client::class);
        assert($client instanceof Client);
        $client->fake();
        $this->instance(Client::class, $client);

        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/tracks')
            ->assertConflict();

    }

    public function testAuthMiddleware(): void
    {
        $this->post('/tracks')
            ->assertUnauthorized();
    }

    public function testCreateTrack(): void
    {
        $provider = $this->providerFactory
            ->createOne();

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($provider) {
            $mock->shouldReceive('image')->andReturn('image1');
            $mock->shouldReceive('provider')->andReturn($provider->name);
            $mock->shouldReceive('id')->andReturn('key1');
        });

        $this->assertDatabaseCount($this->track::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/tracks', [
                'url' => 'https://soundcloud.com/foo/bar',
                'title' => 'track1',
                'image' => 'https://example.com/foo/bar.jpg',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/tracks/'.$this->hashids->encode(1)
            )
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $this->hashids->encode(1))
                    ->where('url', 'https://soundcloud.com/foo/bar')
                    ->where('provider', $provider->name)
                    ->where('title', 'track1')
                    ->where('image', 'https://example.com/foo/bar.jpg')
                    ->where('urge', false)
                    ->where('genres', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount($this->track::class, 1)
            ->assertDatabaseHas($this->track::class, [
                'id' => 1,
                'url' => 'https://soundcloud.com/foo/bar',
                'provider_id' => $provider->id,
                'provider_key' => 'key1',
                'urge' => false,
                'title' => 'track1',
                'image' => 'https://example.com/foo/bar.jpg',
            ]);
    }

    public function testCreateTrackWithSomeEmptyAttributeValues(): void
    {
        $provider = $this->providerFactory
            ->createOne();

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($provider) {
            $mock->shouldReceive('image')->andReturn('image1');
            $mock->shouldReceive('title')->andReturn('track1');
            $mock->shouldReceive('provider')->andReturn($provider->name);
            $mock->shouldReceive('id')->andReturn('key1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->post('/tracks', [
                'url' => 'https://soundcloud.com/foo/bar',
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $this->hashids->encode(1))
                    ->where('url', 'https://soundcloud.com/foo/bar')
                    ->where('provider', $provider->name)
                    ->where('title', 'track1')
                    ->where('image', 'image1')
                    ->where('urge', false)
                    ->where('genres', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas($this->track::class, [
            'id' => 1,
            'url' => 'https://soundcloud.com/foo/bar',
            'provider_id' => $provider->id,
            'provider_key' => 'key1',
            'title' => 'track1',
            'image' => 'image1',
            'urge' => false,
        ]);
    }

    public function testCreateTrackWithGenres(): void
    {
        $pivotTable = $this->track->genres()->getTable();

        $provider = $this->providerFactory
            ->createOne();

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($provider) {
            $mock->shouldReceive('image')->andReturn('image1');
            $mock->shouldReceive('title')->andReturn('track1');
            $mock->shouldReceive('provider')->andReturn($provider->name);
            $mock->shouldReceive('id')->andReturn('key1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->post('/tracks', [
                'url' => 'https://soundcloud.com/foo/bar',
                'genres' => ['genre1', 'genre2'],
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($provider) {
                $json->where('id', $this->hashids->encode(1))
                    ->where('url', 'https://soundcloud.com/foo/bar')
                    ->where('provider', $provider->name)
                    ->where('title', 'track1')
                    ->where('image', 'image1')
                    ->where('urge', false)
                    ->where('genres', ['genre1', 'genre2'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount($this->genre::class, 2)
            ->assertDatabaseHas($this->genre::class, ['name' => 'genre1'])
            ->assertDatabaseHas($this->genre::class, ['name' => 'genre2'])
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['track_id' => 1, 'genre_id' => 1])
            ->assertDatabaseHas($pivotTable, ['track_id' => 1, 'genre_id' => 2]);
    }
}
