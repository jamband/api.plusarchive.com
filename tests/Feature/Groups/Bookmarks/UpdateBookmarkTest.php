<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateBookmarkTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /bookmarks/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /bookmarks/1');
    }

    public function testModelNotFound(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/bookmarks/1', [
                'name' => 'bookmark1',
                'country'=> $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateBookmark(): void
    {
        $bookmark = BookmarkFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Bookmark::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/bookmarks/'.$bookmark->id, [
                'name' => 'updated_bookmark1',
                'country' => $bookmark->country->name,
                'url' => 'https://updated-url1.dev',
                'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($bookmark) {
                $json->where('id', $bookmark->id)
                    ->where('name', 'updated_bookmark1')
                    ->where('country', $bookmark->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', "https://updated-link1.dev\nhttps://updated-link2.dev")
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(Bookmark::class, 1);

        $this->assertDatabaseHas(Bookmark::class, [
            'id' => $bookmark->id,
            'name' => 'updated_bookmark1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
        ]);
    }

    public function testUpdateBookmarkWithSomeEmptyAttributeValues(): void
    {
        $bookmark = BookmarkFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/bookmarks/'.$bookmark->id, [
                'name' => 'updated_bookmark1',
                'country' => $bookmark->country->name,
                'url' => 'https://updated-url1.dev',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($bookmark) {
                $json->where('id', $bookmark->id)
                    ->where('name', 'updated_bookmark1')
                    ->where('country', $bookmark->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', '')
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas(Bookmark::class, [
            'id' => $bookmark->id,
            'name' => 'updated_bookmark1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => '',
        ]);
    }

    public function testUpdateBookmarkWithTags(): void
    {
        $bookmark = BookmarkFactory::new()
            ->createOne();

        $pivotTable = $bookmark->tags()->getTable();

        BookmarkTagFactory::new()
            ->count(4)
            ->state(new Sequence(
                ['name' => 'tag1'],
                ['name' => 'tag2'],
                ['name' => 'tag3'],
                ['name' => 'tag4'],
            ))
            ->create();

        $bookmark->tags()->sync([1, 2]);

        $this->assertDatabaseCount(BookmarkTag::class, 4);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 1]);
        $this->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 2]);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/bookmarks/'.$bookmark->id, [
                'name' => 'updated_bookmark1',
                'country' => $bookmark->country->name,
                'url' => 'https://updated-url1.dev',
                'tags' => ['tag3', 'tag4'],
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($bookmark) {
                $json->where('id', $bookmark->id)
                    ->where('name', 'updated_bookmark1')
                    ->where('country', $bookmark->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', '')
                    ->where('tags', ['tag3', 'tag4'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(BookmarkTag::class, 4);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 3]);
        $this->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 4]);
    }
}
