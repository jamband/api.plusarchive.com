<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Playlists;

use App\Groups\Playlists\Playlist;
use App\Groups\Playlists\PlaylistFactory;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminPlaylistsTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private PlaylistFactory $playlistFactory;
    private Carbon $carbon;
    private Hashids $hashids;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->playlistFactory = new PlaylistFactory();
        $this->carbon = new Carbon();
        $this->hashids = $this->app->make(Hashids::class);
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/playlists/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/playlists/admin')
            ->assertUnauthorized();
    }

    public function testGetAdminPlaylists(): void
    {
        /** @var array<int, Playlist> $playlists */
        $playlists = $this->playlistFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/playlists/admin')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($playlists) {
                $json->where('data.0', [
                    'id' => $this->hashids->encode($playlists[1]->id),
                    'url' => $playlists[1]->url,
                    'provider' => $playlists[1]->provider->name,
                    'title' => $playlists[1]->title,
                    'created_at' => $playlists[1]->created_at->format('Y-m-d H:i'),
                    'updated_at' => $playlists[1]->updated_at->format('Y-m-d H:i'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminPlaylistsWithSortAsc(): void
    {
        /** @var array<int, Playlist> $playlists */
        $playlists = $this->playlistFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'title' => 'title'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/playlists/admin?sort=title')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($playlists) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $this->hashids->encode($playlists[0]->id))
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminPlaylistsWithSortDesc(): void
    {
        /** @var array<int, Playlist> $playlists */
        $playlists = $this->playlistFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'title' => 'title'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/playlists/admin?sort=-title')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($playlists) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $this->hashids->encode($playlists[1]->id))
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminPlaylistsWithSearchTitle(): void
    {
        /** @var array<int, Playlist> $playlists */
        $playlists = $this->playlistFactory
            ->count(3)
            ->state(new Sequence(
                ['title' => 'foo'],
                ['title' => 'bar'],
                ['title' => 'baz'],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index),
            ]))
        ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/playlists/admin?title=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($playlists) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $this->hashids->encode($playlists[2]->id))
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/playlists/admin?title[]=provider=[]&sort[]=')
            ->assertOk();
    }
}
