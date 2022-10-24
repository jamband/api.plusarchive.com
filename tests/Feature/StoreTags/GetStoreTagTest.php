<?php

declare(strict_types=1);

namespace Tests\Feature\StoreTags;

use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetStoreTagTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /store-tags/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /store-tags/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/store-tags/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetStoreTag(): void
    {
        $tag = StoreTagFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/store-tags/'.$tag->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($tag) {
                $json->where('id', $tag->id)
                    ->where('name', $tag->name);
            });
    }
}
