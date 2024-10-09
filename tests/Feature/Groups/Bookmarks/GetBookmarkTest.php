<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Bookmarks;

use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetBookmarkTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private BookmarkFactory $bookmarkFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->bookmarkFactory = new BookmarkFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/bookmarks/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/bookmarks/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmarks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testGetBookmark(): void
    {
        $bookmark = $this->bookmarkFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/bookmarks/'.$bookmark->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($bookmark) {
                $json->where('id', $bookmark->id)
                    ->where('name', $bookmark->name)
                    ->where('country', $bookmark->country->name)
                    ->where('url', $bookmark->url)
                    ->where('links', $bookmark->links)
                    ->where('tags', [])
                    ->where('created_at', $bookmark->created_at->format('Y-m-d H:i'))
                    ->where('updated_at', $bookmark->updated_at->format('Y-m-d H:i'));
            });
    }
}
