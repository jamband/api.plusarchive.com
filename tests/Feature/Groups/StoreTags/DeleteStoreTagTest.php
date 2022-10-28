<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\StoreTags;

use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteStoreTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /store-tags/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /store-tags/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/store-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteStoreTag(): void
    {
        $tag = StoreTagFactory::new()
            ->createOne();

        $this->assertDatabaseCount(StoreTag::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/store-tags/'.$tag->id)
            ->assertNoContent();

        $this->assertDatabaseCount(StoreTag::class, 0);
    }
}
