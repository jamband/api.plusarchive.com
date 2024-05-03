<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\BookmarkTags\BookmarkTagFactory;
use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateBookmarkTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private CountryFactory $countryFactory;
    private BookmarkFactory $bookmarkFactory;
    private BookmarkTagFactory $tagFactory;
    private BookmarkTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->countryFactory = new CountryFactory();
        $this->bookmarkFactory = new BookmarkFactory();
        $this->tagFactory = new BookmarkTagFactory();
        $this->tag = new BookmarkTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->put('/bookmarks/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/bookmarks/1')
            ->assertUnauthorized();
    }

    public function testModelNotFound(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->put('/bookmarks/1', [
                'name' => 'bookmark1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateBookmark(): void
    {
        $bookmark = $this->bookmarkFactory
            ->createOne();

        $this->assertDatabaseCount($bookmark::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/bookmarks/'.$bookmark->id, [
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

        $this->assertDatabaseCount($bookmark::class, 1)
            ->assertDatabaseHas($bookmark::class, [
                'id' => $bookmark->id,
                'name' => 'updated_bookmark1',
                'country_id' => 1,
                'url' => 'https://updated-url1.dev',
                'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
            ]);
    }

    public function testUpdateBookmarkWithSomeEmptyAttributeValues(): void
    {
        $bookmark = $this->bookmarkFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->put('/bookmarks/'.$bookmark->id, [
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

        $this->assertDatabaseHas($bookmark::class, [
            'id' => $bookmark->id,
            'name' => 'updated_bookmark1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => '',
        ]);
    }

    public function testUpdateBookmarkWithTags(): void
    {
        $bookmark = $this->bookmarkFactory
            ->createOne();

        $pivotTable = $bookmark->tags()->getTable();

        $this->tagFactory
            ->count(4)
            ->state(new Sequence(
                ['name' => 'tag1'],
                ['name' => 'tag2'],
                ['name' => 'tag3'],
                ['name' => 'tag4'],
            ))
            ->create();

        $bookmark->tags()->sync([1, 2]);

        $this->assertDatabaseCount($this->tag::class, 4)
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 1])
            ->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 2]);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/bookmarks/'.$bookmark->id, [
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

        $this->assertDatabaseCount($this->tag::class, 4)
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 3])
            ->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 4]);
    }
}
