<?php

declare(strict_types=1);

namespace Tests\Feature\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateBookmarkTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /bookmarks');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /bookmarks');
    }

    public function testCreateBookmark(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Bookmark::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/bookmarks', [
                'name' => 'bookmark1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
                'links' => "https://link1.dev\nhttps://link2.dev",
            ])
            ->assertCreated()
            ->assertHeader('Location', $this->app['config']['app.url'].'/bookmarks/1')
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'bookmark1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', "https://link1.dev\nhttps://link2.dev")
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(Bookmark::class, 1);

        $this->assertDataBaseHas(Bookmark::class, [
            'id' => 1,
            'name' => 'bookmark1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => "https://link1.dev\nhttps://link2.dev",
        ]);
    }

    public function testCreateBookmarkWithSomeEmptyAttributeValues(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/bookmarks', [
                'name' => 'bookmark1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'bookmark1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', '')
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas(Bookmark::class, [
            'id' => 1,
            'name' => 'bookmark1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => '',
        ]);
    }

    public function testCreateBookmarkWithTags(): void
    {
        $bookmark = new Bookmark();
        $pivotTable = $bookmark->tags()->getTable();

        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/bookmarks', [
                'name' => 'bookmark1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
                'tags' => ['tag1', 'tag2'],
            ])
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', 1)
                    ->where('name', 'bookmark1')
                    ->where('country', $country->name)
                    ->where('url', 'https://url1.dev')
                    ->where('links', '')
                    ->where('tags', ['tag1', 'tag2'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount(BookmarkTag::class, 2);
        $this->assertDatabaseHas(BookmarkTag::class, ['name' => 'tag1']);
        $this->assertDatabaseHas(BookmarkTag::class, ['name' => 'tag2']);

        $this->assertDatabaseCount($pivotTable, 2);
        $this->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 1]);
        $this->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 2]);
    }
}
