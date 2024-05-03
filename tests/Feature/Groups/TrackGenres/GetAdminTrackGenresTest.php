<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\TrackGenres;

use App\Groups\TrackGenres\TrackGenre;
use App\Groups\TrackGenres\TrackGenreFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminTrackGenresTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private TrackGenreFactory $genreFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->genreFactory = new TrackGenreFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/track-genres/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/track-genres/admin')
            ->assertUnauthorized();
    }

    public function testGetAdminTrackGenres(): void
    {
        /** @var array<int, TrackGenre> $genres */
        $genres = $this->genreFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/track-genres/admin')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson(function (AssertableJson $json) use ($genres) {
                $json->where('data.0', [
                    'id' => $genres[2]->id,
                    'name' => $genres[2]->name,
                ]);

                $json->where('data.1', [
                    'id' => $genres[1]->id,
                    'name' => $genres[1]->name,
                ]);

                $json->where('data.2', [
                    'id' => $genres[0]->id,
                    'name' => $genres[0]->name,
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 3)
                    ->etc());
            });
    }

    public function testGetAdminTrackGenresWithSortAsc(): void
    {
        /** @var array<int, TrackGenre> $genres */
        $genres = $this->genreFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/track-genres/admin?sort=name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($genres) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $genres[0]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminTrackGenresWithSortDesc(): void
    {
        /** @var array<int, TrackGenre> $genres */
        $genres = $this->genreFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/track-genres/admin?sort=-name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($genres) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $genres[1]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminTrackGenresWithName(): void
    {
        /** @var array<int, TrackGenre> $genres */
        $genres = $this->genreFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/track-genres/admin?name=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($genres) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $genres[2]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/track-genres/admin?name[]=&sort[]=')
            ->assertOk();
    }
}
