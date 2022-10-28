<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Countries\CountryFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetBookmarksTest extends TestCase
{
    use RefreshDatabase;

    public function testGetBookmarks(): void
    {
        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = BookmarkFactory::new()
            ->count(2)
            ->hasAttached(
                factory: BookmarkTagFactory::new()
                    ->count(2),
                relationship: 'tags',
            )
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->getJson('/bookmarks')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->where('data.0', [
                    'name' => $bookmarks[1]->name,
                    'url' => $bookmarks[1]->url,
                    'links' => $bookmarks[1]->links,
                    'country' => $bookmarks[1]->country->name,
                    'tags' => [
                        $bookmarks[1]->tags[0]->name,
                        $bookmarks[1]->tags[1]->name,
                    ],
                ]);

                $json->where('data.1', [
                    'name' => $bookmarks[0]->name,
                    'url' => $bookmarks[0]->url,
                    'links' => $bookmarks[0]->links,
                    'country' => $bookmarks[0]->country->name,
                    'tags' => [
                        $bookmarks[0]->tags[0]->name,
                        $bookmarks[0]->tags[1]->name,
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

    public function testGetBookmarksWithCountry(): void
    {
        BookmarkFactory::new()
            ->for(
                CountryFactory::new()->state([
                    'name' => 'foo',
                ]),
            )
            ->createOne();

        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = BookmarkFactory::new()
            ->count(2)
            ->for(
                CountryFactory::new()->state([
                    'name' => 'bar',
                ]),
            )
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->getJson('/bookmarks?country=bar')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->where('data.0', [
                    'name' => $bookmarks[1]->name,
                    'url' => $bookmarks[1]->url,
                    'links' => $bookmarks[1]->links,
                    'country' => 'bar',
                    'tags' => [],
                ])
                ->where('data.1', [
                    'name' => $bookmarks[0]->name,
                    'url' => $bookmarks[0]->url,
                    'links' => $bookmarks[0]->links,
                    'country' => 'bar',
                    'tags' => [],
                ])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetBookmarksWithUnmatchedCountry(): void
    {
        BookmarkFactory::new()
            ->for(
                CountryFactory::new()->state([
                    'name' => 'foo',
                ]),
            )
            ->createOne();

        $this->getJson('/bookmarks?country=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetBookmarksWithTag(): void
    {
        BookmarkFactory::new()
            ->hasAttached(
                factory: BookmarkTagFactory::new()
                    ->state(['name' => 'foo']),
                relationship: 'tags',
            )
            ->createOne();

        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = BookmarkFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        BookmarkTagFactory::new()
            ->state(['name' => 'bar'])
            ->createOne();

        $bookmarks[0]->tags()->sync([2]);
        $bookmarks[1]->tags()->sync([2]);

        $this->getJson('/bookmarks?tag=bar')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->where('data.0', [
                    'name' => $bookmarks[1]->name,
                    'url' => $bookmarks[1]->url,
                    'links' => $bookmarks[1]->links,
                    'country' => $bookmarks[1]->country->name,
                    'tags' => ['bar'],
                ]);

                $json->where('data.1', [
                    'name' => $bookmarks[0]->name,
                    'url' => $bookmarks[0]->url,
                    'links' => $bookmarks[0]->links,
                    'country' => $bookmarks[0]->country->name,
                    'tags' => ['bar'],
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetBookmarksWithUnmatchedTag(): void
    {
        BookmarkFactory::new()
            ->hasAttached(
                factory: BookmarkTagFactory::new()
                    ->state(['name' => 'foo']),
                relationship: 'tags',
            )
            ->createOne();

        $this->getJson('/bookmarks?tag=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetBookmarksWithCountryAndTag(): void
    {
        CountryFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'country'.($sequence->index + 1),
            ]))
            ->create();

        /** @var array<int, Bookmark> $bookmarks */
        $bookmarks = BookmarkFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['country_id' => 1],
                ['country_id' => 1],
                ['country_id' => 1],
                ['country_id' => 2],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => (new Carbon())->addMinutes($sequence->index),
            ]))
            ->create();

        BookmarkTagFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'tag'.($sequence->index + 1),
            ]))
            ->create();

        $bookmarks[0]->tags()->sync([1]);
        $bookmarks[1]->tags()->sync([1]);

        $this->getJson('/bookmarks?country=country1&tag=tag1')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($bookmarks) {
                $json->where('data.0', [
                    'name' => $bookmarks[1]->name,
                    'url' => $bookmarks[1]->url,
                    'links' => $bookmarks[1]->links,
                    'country' => 'country1',
                    'tags' => ['tag1'],
                ]);

                $json->where('data.1', [
                    'name' => $bookmarks[0]->name,
                    'url' => $bookmarks[0]->url,
                    'links' => $bookmarks[0]->links,
                    'country' => 'country1',
                    'tags' => ['tag1'],
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->getJson('/bookmarks?country[]=&tag[]=')
            ->assertOk();
    }
}
