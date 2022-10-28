<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetSearchBookmarksTest extends TestCase
{
    use RefreshDatabase;

    public function testGetSearchBookmarks(): void
    {
        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = BookmarkFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->hasAttached(
                factory: BookmarkTagFactory::new()
                    ->count(2),
                relationship: 'tags',
            )
            ->create();

        $this->getJson('/bookmarks/search?q=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->where('data.0', [
                    'name' => $bookmarks[1]->name,
                    'country' => $bookmarks[1]->country->name,
                    'url' => $bookmarks[1]->url,
                    'links' => $bookmarks[1]->links,
                    'tags' => [
                        $bookmarks[1]->tags[0]->name,
                        $bookmarks[1]->tags[1]->name,
                    ],
                ]);

                $json->where('data.1', [
                    'name' => $bookmarks[2]->name,
                    'country' => $bookmarks[2]->country->name,
                    'url' => $bookmarks[2]->url,
                    'links' => $bookmarks[2]->links,
                    'tags' => [
                        $bookmarks[2]->tags[0]->name,
                        $bookmarks[2]->tags[1]->name,
                    ],
                ]);

                $json->where('pagination', [
                    'currentPage' => 1,
                    'lastPage' => 1,
                    'perPage' => 14,
                    'total' => 2,
                ]);
            });
    }

    public function testGetSearchBookmarksWithoutParameter(): void
    {
        BookmarkFactory::new()
            ->createOne();

        $this->getJson('/bookmarks/search')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetSearchBookmarksWithUnmatchedSearch(): void
    {
        BookmarkFactory::new()
            ->state(['name' => 'foo'])
            ->createOne();

        $this->getJson('/bookmarks/search?q=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testQueryStringTypes(): void
    {
        $this->getJson('/bookmarks/search?q[]=')
            ->assertOk();
    }
}
