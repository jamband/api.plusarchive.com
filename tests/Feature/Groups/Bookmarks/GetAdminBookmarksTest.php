<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminBookmarksTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private BookmarkFactory $bookmarkFactory;
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->bookmarkFactory = new BookmarkFactory();
        $this->carbon = new Carbon();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/bookmarks/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/bookmarks/admin')
            ->assertUnauthorized();
    }

    public function testGetAdminBookmarks(): void
    {
        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = $this->bookmarkFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmarks/admin')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->where('data.0', [
                    'id' => $bookmarks[1]->id,
                    'name' => $bookmarks[1]->name,
                    'country' => $bookmarks[1]->country->name,
                    'url' => $bookmarks[1]->url,
                    'links' => $bookmarks[1]->links,
                    'tags' => [],
                    'created_at' => $bookmarks[1]->created_at->format('Y-m-d H:i'),
                    'updated_at' => $bookmarks[1]->updated_at->format('Y-m-d H:i'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminBookmarksWithSortAsc(): void
    {
        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = $this->bookmarkFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmarks/admin?sort=name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $bookmarks[0]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminBookmarksWithSortDesc(): void
    {
        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = $this->bookmarkFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmarks/admin?sort=-name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $bookmarks[1]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminBookmarksWithName(): void
    {
        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = $this->bookmarkFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmarks/admin?name=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $bookmarks[2]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmarks/admin?name[]=&country[]=&tag[]=&sort[]=')
            ->assertOk();
    }
}
