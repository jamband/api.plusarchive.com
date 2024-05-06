<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Tracks;

use App\Groups\Tracks\Track;
use App\Groups\Tracks\TrackFactory;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminTracksTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackFactory $trackFactory;
    private Carbon $carbon;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->trackFactory = new TrackFactory();
        $this->carbon = new Carbon();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/tracks/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/tracks/admin')
            ->assertUnauthorized();
    }

    public function testGetAdminTracks(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon)->addMinutes($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/tracks/admin')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tracks) {
                $json->where('data.0', [
                    'id' => $this->hashids->encode($tracks[1]->id),
                    'url' => $tracks[1]->url,
                    'provider' => $tracks[1]->provider->name,
                    'title' => $tracks[1]->title,
                    'image' => $tracks[1]->image,
                    'genres' => [],
                    'urge' => false,
                    'created_at' => $tracks[1]->created_at->format('Y-m-d H:i'),
                    'updated_at' => $tracks[1]->updated_at->format('Y-m-d H:i'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminTracksWithSortAsc(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'title' => 'title'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/tracks/admin?sort=title')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tracks) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $this->hashids->encode($tracks[0]->id))
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminTracksWithSortDesc(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'title' => 'title'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/tracks/admin?sort=-title')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tracks) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $this->hashids->encode($tracks[1]->id))
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminTracksWithName(): void
    {
        /** @var array<int, Track> $tracks */
        $tracks = $this->trackFactory
            ->count(3)
            ->state(new Sequence(
                ['title' => 'foo'],
                ['title' => 'bar'],
                ['title' => 'baz'],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon)->addMinutes($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/tracks/admin?title=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tracks) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $this->hashids->encode($tracks[2]->id))
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/tracks/admin?provider[]=&title[]=&urge[]=&genre[]=&sort[]=')
            ->assertOk();
    }
}
