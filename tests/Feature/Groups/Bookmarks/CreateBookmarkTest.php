<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\Bookmark;
use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateBookmarkTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private CountryFactory $countryFactory;
    private Bookmark $bookmark;
    private BookmarkTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->countryFactory = new CountryFactory();
        $this->bookmark = new Bookmark();
        $this->tag = new BookmarkTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/bookmarks')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/bookmarks')
            ->assertUnauthorized();
    }

    public function testCreateBookmark(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->assertDatabaseCount($this->bookmark::class, 0);

        $this->actingAs($this->userFactory->makeOne())
            ->post('/bookmarks', [
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

        $this->assertDatabaseCount($this->bookmark::class, 1)
            ->assertDataBaseHas($this->bookmark::class, [
                'id' => 1,
                'name' => 'bookmark1',
                'country_id' => $country->id,
                'url' => 'https://url1.dev',
                'links' => "https://link1.dev\nhttps://link2.dev",
            ]);
    }

    public function testCreateBookmarkWithSomeEmptyAttributeValues(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->post('/bookmarks', [
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

        $this->assertDatabaseHas($this->bookmark::class, [
            'id' => 1,
            'name' => 'bookmark1',
            'country_id' => $country->id,
            'url' => 'https://url1.dev',
            'links' => '',
        ]);
    }

    public function testCreateBookmarkWithTags(): void
    {
        $pivotTable = $this->bookmark->tags()->getTable();

        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->post('/bookmarks', [
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

        $this->assertDatabaseCount($this->tag::class, 2)
            ->assertDatabaseHas($this->tag::class, ['name' => 'tag1'])
            ->assertDatabaseHas($this->tag::class, ['name' => 'tag2']);

        $this->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 1])
            ->assertDatabaseHas($pivotTable, ['bookmark_id' => 1, 'tag_id' => 2]);
    }
}
