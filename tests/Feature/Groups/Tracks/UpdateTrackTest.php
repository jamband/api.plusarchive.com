<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Tracks\TrackFactory;
use App\Groups\Users\UserFactory;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory as Client;
use Illuminate\Testing\Fluent\AssertableJson;
use Jamband\Ripple\Ripple;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateTrackTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackFactory $trackFactory;
    private TrackGenreFactory $genreFactory;
    private TrackGenre $genre;
    private Ripple $ripple;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->trackFactory = new TrackFactory();
        $this->genreFactory = new TrackGenreFactory();
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
            ->put('/tracks/'.str_repeat('a', 11))
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/tracks/'.str_repeat('a', 11))
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->put('/tracks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);

        $this->partialMock($this->ripple::class, function (MockInterface $mock) {
            $mock->shouldReceive('id')->andReturn('updated_key1');
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('title')->andReturn('updated_title1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/tracks/'.$this->hashids->encode(1), [
                'url' => 'https://soundcloud.com/updated-foo/updated-bar',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testNotFoundWithInvalidHashValue(): void
    {
        $this->partialMock($this->ripple::class, function (MockInterface $mock) {
            $mock->shouldReceive('id')->andReturn('updated_key1');
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('title')->andReturn('updated_title1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/tracks/'.str_repeat('a', 11), [
                'url' => 'https://soundcloud.com/updated-foo/updated-bar',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testUpdateTrack(): void
    {
        $track = $this->trackFactory
            ->createOne();

        $this->assertDatabaseCount($track::class, 1);

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($track) {
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('provider')->andReturn($track->provider->name);
            $mock->shouldReceive('id')->andReturn('updated_key1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/tracks/'.$this->hashids->encode($track->id), [
                'url' => 'https://soundcloud.com/updated-foo/updated-bar',
                'title' => 'updated_track1',
                'image' => 'https://example.com/updated-foo/updated-bar.jpg',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($track) {
                $json->where('id', $this->hashids->encode($track->id))
                    ->where('url', 'https://soundcloud.com/updated-foo/updated-bar')
                    ->where('provider', $track->provider->name)
                    ->where('title', 'updated_track1')
                    ->where('image', 'https://example.com/updated-foo/updated-bar.jpg')
                    ->where('urge', false)
                    ->where('genres', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount($track::class, 1)
            ->assertDatabaseHas($track::class, [
                'id' => $track->id,
                'url' => 'https://soundcloud.com/updated-foo/updated-bar',
                'provider_id' => $track->provider_id,
                'provider_key' => 'updated_key1',
                'title' => 'updated_track1',
                'image' => 'https://example.com/updated-foo/updated-bar.jpg',
                'urge' => false,
            ]);
    }

    public function testUpdateTrackWithUrgeIsTrue(): void
    {
        $track = $this->trackFactory
            ->createOne(['urge' => true]);

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($track) {
            $mock->shouldReceive('image')->andReturn($track->image);
            $mock->shouldReceive('provider')->andReturn($track->provider->name);
            $mock->shouldReceive('id')->andReturn($track->provider_key);
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/tracks/'.$this->hashids->encode($track->id), [
                'title' => $track->title,
                'url' => 'https://soundcloud.com/updated-foo/updated-bar',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($track) {
                $json->where('id', $this->hashids->encode($track->id))
                    ->where('urge', true)
                    ->etc();
            });

        $this->assertDatabaseHas($track::class, [
            'id' => $track->id,
            'urge' => true,
        ]);
    }

    public function testUpdateTrackWithSomeEmptyAttributeValues(): void
    {
        $track = $this->trackFactory
            ->createOne();

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($track) {
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('title')->andReturn('updated_track1');
            $mock->shouldReceive('provider')->andReturn($track->provider->name);
            $mock->shouldReceive('id')->andReturn('updated_key1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/tracks/'.$this->hashids->encode($track->id), [
                'url' => 'https://soundcloud.com/updated-foo/updated-bar',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($track) {
                $json->where('id', $this->hashids->encode($track->id))
                    ->where('url', 'https://soundcloud.com/updated-foo/updated-bar')
                    ->where('provider', $track->provider->name)
                    ->where('title', 'updated_track1')
                    ->where('image', 'updated-image1')
                    ->where('urge', false)
                    ->where('genres', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas($track::class, [
            'id' => $track->id,
            'url' => 'https://soundcloud.com/updated-foo/updated-bar',
            'provider_id' => $track->provider_id,
            'provider_key' => 'updated_key1',
            'title' => 'updated_track1',
            'image' => 'updated-image1',
            'urge' => false,
        ]);
    }

    public function testUpdateTrackWithGenres(): void
    {
        $track = $this->trackFactory
            ->createOne();

        $pivotTable = $track->genres()->getTable();

        $this->genreFactory
            ->count(4)
            ->state(new Sequence(
                ['name' => 'genre1'],
                ['name' => 'genre2'],
                ['name' => 'genre3'],
                ['name' => 'genre4'],
            ))
            ->create();

        $track->genres()->sync([1, 2]);

        $this->assertDatabaseCount($this->genre::class, 4)
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['track_id' => 1, 'genre_id' => 1])
            ->assertDatabaseHas($pivotTable, ['track_id' => 1, 'genre_id' => 2]);

        $this->partialMock($this->ripple::class, function (MockInterface $mock) use ($track) {
            $mock->shouldReceive('image')->andReturn('updated-image1');
            $mock->shouldReceive('title')->andReturn('updated_track1');
            $mock->shouldReceive('provider')->andReturn($track->provider->name);
            $mock->shouldReceive('id')->andReturn('updated_key1');
        });

        $this->actingAs($this->userFactory->makeOne())
            ->put('/tracks/'.$this->hashids->encode($track->id), [
                'url' => 'https://soundcloud.com/updated-foo/updated-bar',
                'genres' => ['genre3', 'genre4'],
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($track) {
                $json->where('id', $this->hashids->encode($track->id))
                    ->where('url', 'https://soundcloud.com/updated-foo/updated-bar')
                    ->where('provider', $track->provider->name)
                    ->where('title', 'updated_track1')
                    ->where('image', 'updated-image1')
                    ->where('urge', false)
                    ->where('genres', ['genre3', 'genre4'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount($this->genre::class, 4)
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['track_id' => 1, 'genre_id' => 3])
            ->assertDatabaseHas($pivotTable, ['track_id' => 1, 'genre_id' => 4]);
    }
}
