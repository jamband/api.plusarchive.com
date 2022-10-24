<?php

declare(strict_types=1);

namespace Tests\Feature\StoreTags;

use App\Groups\StoreTags\StoreTag;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateStoreTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /store-tags');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /store-tags');
    }

    public function testCreateStoreTag(): void
    {
        $this->assertDatabaseCount(StoreTag::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/store-tags', [
                'name' => 'tag1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/store-tags/1'
            )
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', 1)
                ->where('name', 'tag1'));

        $this->assertDatabaseCount(StoreTag::class, 1);

        $this->assertDatabaseHas(StoreTag::class, [
            'id' => 1,
            'name' => 'tag1',
        ]);
    }
}
