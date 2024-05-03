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

    private BookmarkFactory $bookmarkFactory;
    private BookmarkTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookmarkFactory = new BookmarkFactory();
        $this->tagFactory = new BookmarkTagFactory();
    }

    public function testGetSearchBookmarks(): void
    {
        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = $this->bookmarkFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->hasAttached(
                factory: $this->tagFactory
                    ->count(2),
                relationship: 'tags',
            )
            ->create();

        $this->get('/bookmarks/search?q=ba')
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
        $this->bookmarkFactory
            ->createOne();

        $this->get('/bookmarks/search')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetSearchBookmarksWithUnmatchedSearch(): void
    {
        $this->bookmarkFactory
            ->state(['name' => 'foo'])
            ->createOne();

        $this->get('/bookmarks/search?q=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testQueryStringTypes(): void
    {
        $this->get('/bookmarks/search?q[]=')
            ->assertOk();
    }
}
