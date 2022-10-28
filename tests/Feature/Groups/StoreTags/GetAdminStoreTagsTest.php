<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\StoreTags;

use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetAdminStoreTagsTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /store-tags/admin');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /store-tags/admin');
    }

    public function testGetAdminStoreTags(): void
    {
        /** @var array<int, StoreTag> $tags */
        $tags = StoreTagFactory::new()
            ->count(3)
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/store-tags/admin')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson(function (AssertableJson $json) use ($tags) {
                $json->where('data.0', [
                    'id' => $tags[2]->id,
                    'name' => $tags[2]->name,
                ]);

                $json->where('data.1', [
                    'id' => $tags[1]->id,
                    'name' => $tags[1]->name,
                ]);

                $json->where('data.2', [
                    'id' => $tags[0]->id,
                    'name' => $tags[0]->name,
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 3)
                    ->etc());
            });
    }

    public function testGetAdminStoreTagsWithSortAsc(): void
    {
        /** @var array<int, StoreTag> $tags */
        $tags = StoreTagFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/store-tags/admin?sort=name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tags) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $tags[0]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminStoreTagsWithSortDesc(): void
    {
        /** @var array<int, StoreTag> $tags */
        $tags = StoreTagFactory::new()
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/store-tags/admin?sort=-name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tags) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $tags[1]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminStoreTagsWithName(): void
    {
        /** @var array<int, StoreTag> $tags */
        $tags = StoreTagFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/store-tags/admin?name=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($tags) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $tags[2]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/store-tags/admin?name[]=&sort[]=')
            ->assertOk();
    }
}
