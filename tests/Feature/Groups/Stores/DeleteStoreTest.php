<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Stores\Store;
use App\Groups\Stores\StoreFactory;
use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteStoreTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /stores/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /stores/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/stores/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteStore(): void
    {
        $store = StoreFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Store::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/stores/'.$store->id)
            ->assertNoContent();

        $this->assertDatabaseCount(Store::class, 0);
    }

    public function testDeleteStoreWithTags(): void
    {
        $store = StoreFactory::new()
            ->createOne();

        $pivotTable = $store->tags()->getTable();

        StoreTagFactory::new()
            ->count(2)
            ->create();

        $store->tags()->sync([1, 2]);

        $this->assertDatabaseCount(Store::class, 1);
        $this->assertDatabaseCount(StoreTag::class, 2);
        $this->assertDatabaseCount($pivotTable, 2);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/stores/'.$store->id)
            ->assertNoContent();

        $this->assertDatabaseCount(Store::class, 0);
        $this->assertDatabaseCount(StoreTag::class, 2);
        $this->assertDatabaseCount($pivotTable, 0);
    }
}
