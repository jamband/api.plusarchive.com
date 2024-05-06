<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Countries\CountryFactory;
use App\Groups\Stores\StoreFactory;
use App\Groups\StoreTags\StoreTag;
use App\Groups\StoreTags\StoreTagFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateStoreTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private CountryFactory $countryFactory;
    private StoreFactory $storeFactory;
    private StoreTagFactory $tagFactory;
    private StoreTag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->countryFactory = new CountryFactory();
        $this->storeFactory = new StoreFactory();
        $this->tagFactory = new StoreTagFactory();
        $this->tag = new StoreTag();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->put('/stores/1')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/stores/1')
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $country = $this->countryFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->put('/stores/1', [
                'name' => 'store1',
                'country' => $country->name,
                'url' => 'https://url1.dev',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testUpdateStore(): void
    {
        $store = $this->storeFactory
            ->createOne();

        $this->assertDatabaseCount($store::class, 1);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/stores/'.$store->id, [
                'name' => 'updated_store1',
                'country' => $store->country->name,
                'url' => 'https://updated-url1.dev',
                'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($store) {
                $json->where('id', $store->id)
                    ->where('name', 'updated_store1')
                    ->where('country', $store->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', "https://updated-link1.dev\nhttps://updated-link2.dev")
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount($store::class, 1)
            ->assertDatabaseHas($store::class, [
                'id' => $store->id,
                'name' => 'updated_store1',
                'country_id' => 1,
                'url' => 'https://updated-url1.dev',
                'links' => "https://updated-link1.dev\nhttps://updated-link2.dev",
            ]);
    }

    public function testUpdateStoreWithSomeEmptyAttributeValues(): void
    {
        $store = $this->storeFactory
            ->createOne();

        $this->actingAs($this->userFactory->makeOne())
            ->put('/stores/'.$store->id, [
                'name' => 'updated_store1',
                'country' => $store->country->name,
                'url' => 'https://updated-url1.dev',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($store) {
                $json->where('id', $store->id)
                    ->where('name', 'updated_store1')
                    ->where('country', $store->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', '')
                    ->where('tags', [])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseHas($store::class, [
            'id' => $store->id,
            'name' => 'updated_store1',
            'country_id' => 1,
            'url' => 'https://updated-url1.dev',
            'links' => '',
        ]);
    }

    public function testUpdateStoreWithTags(): void
    {
        $store = $this->storeFactory
            ->createOne();

        $pivotTable = $store->tags()->getTable();

        $this->tagFactory
            ->count(4)
            ->state(new Sequence(
                ['name' => 'tag1'],
                ['name' => 'tag2'],
                ['name' => 'tag3'],
                ['name' => 'tag4'],
            ))
            ->create();

        $store->tags()->sync([1, 2]);

        $this->assertDatabaseCount($this->tag::class, 4)
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 1])
            ->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 2]);

        $this->actingAs($this->userFactory->makeOne())
            ->put('/stores/'.$store->id, [
                'name' => 'updated_store1',
                'country' => $store->country->name,
                'url' => 'https://updated-url1.dev',
                'tags' => ['tag3', 'tag4'],
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($store) {
                $json->where('id', $store->id)
                    ->where('name', 'updated_store1')
                    ->where('country', $store->country->name)
                    ->where('url', 'https://updated-url1.dev')
                    ->where('links', '')
                    ->where('tags', ['tag3', 'tag4'])
                    ->has('created_at')
                    ->has('updated_at');
            });

        $this->assertDatabaseCount($this->tag::class, 4)
            ->assertDatabaseCount($pivotTable, 2)
            ->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 3])
            ->assertDatabaseHas($pivotTable, ['store_id' => 1, 'tag_id' => 4]);
    }
}
