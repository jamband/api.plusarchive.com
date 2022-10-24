<?php

declare(strict_types=1);

namespace Tests\Feature\Bookmarks;

use App\Groups\Bookmarks\BookmarkFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetBookmarkTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /bookmarks/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /bookmarks/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/bookmarks/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetBookmark(): void
    {
        $bookmark = BookmarkFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/bookmarks/'.$bookmark->id)
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
