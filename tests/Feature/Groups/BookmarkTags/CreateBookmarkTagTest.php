<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\BookmarkTags;

use App\Groups\BookmarkTags\BookmarkTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateBookmarkTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /bookmark-tags');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /bookmark-tags');
    }

    public function testCreateBookmarkTag(): void
    {
        $this->assertDatabaseCount(BookmarkTag::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/bookmark-tags', [
                'name' => 'tag1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/bookmark-tags/1'
            )
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', 1)
                ->where('name', 'tag1'));

        $this->assertDatabaseCount(BookmarkTag::class, 1);

        $this->assertDatabaseHas(BookmarkTag::class, [
            'id' => 1,
            'name' => 'tag1',
        ]);
    }
}
