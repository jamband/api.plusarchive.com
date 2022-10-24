<?php

declare(strict_types=1);

namespace Tests\Feature\StoreTags;

use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateStoreTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /store-tags/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /store-tags/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/store-tags/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateStoreTag(): void
    {
        $tag = StoreTagFactory::new()
            ->createOne();

        $this->assertDatabaseCount(StoreTag::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/store-tags/'.$tag->id, [
                'name' => 'updated_tag1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', 'updated_tag1');
            });

        $this->assertDatabaseCount(StoreTag::class, 1);

        $this->assertDatabaseHas(StoreTag::class, [
            'id' => $tag->id,
            'name' => 'updated_tag1',
        ]);
    }
}
