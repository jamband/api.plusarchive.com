<?php

declare(strict_types=1);

namespace Tests\Feature\Playlists;

use App\Groups\Playlists\Playlist;
use App\Groups\Playlists\PlaylistFactory;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetAdminPlaylistsTest extends TestCase
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
        $this->assertVerifiedMiddleware('GET /playlists/admin');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /playlists/admin');
    }

    public function testGetAdminPlaylists(): void
    {
        /** @var array<int, Playlist> $playlists */
        $playlists = PlaylistFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/playlists/admin')
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
        $playlists = PlaylistFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'title' => 'title'.($sequence->index),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/playlists/admin?sort=title')
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
        $playlists = PlaylistFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'title' => 'title'.($sequence->index),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/playlists/admin?sort=-title')
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
        $playlists = PlaylistFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['title' => 'foo'],
                ['title' => 'bar'],
                ['title' => 'baz'],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
        ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/playlists/admin?title=ba')
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
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/playlists/admin?title[]=provider=[]&sort[]=')
            ->assertOk();
    }
}
