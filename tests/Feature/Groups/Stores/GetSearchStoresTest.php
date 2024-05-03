<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Stores\Store;
use App\Groups\Stores\StoreFactory;
use App\Groups\StoreTags\StoreTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetSearchStoresTest extends TestCase
{
    use RefreshDatabase;

    private StoreFactory $storeFactory;
    private StoreTagFactory $tagFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storeFactory = new StoreFactory();
        $this->tagFactory = new StoreTagFactory();
    }

    public function testGetSearchStores(): void
    {
        /** @var array<int, Store> $stores */
        $stores = $this->storeFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->hasAttached(
                factory: $this->tagFactory
                    ->count(2),
                relationship: 'tags',
            )
            ->create();

        $this->get('/stores/search?q=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->where('data.0', [
                    'name' => $stores[1]->name,
                    'country' => $stores[1]->country->name,
                    'url' => $stores[1]->url,
                    'links' => $stores[1]->links,
                    'tags' => [
                        $stores[1]->tags[0]->name,
                        $stores[1]->tags[1]->name,
                    ],
                ]);

                $json->where('data.1', [
                    'name' => $stores[2]->name,
                    'country' => $stores[2]->country->name,
                    'url' => $stores[2]->url,
                    'links' => $stores[2]->links,
                    'tags' => [
                        $stores[2]->tags[0]->name,
                        $stores[2]->tags[1]->name,
                    ],
                ]);

                $json->where('pagination', [
                    'currentPage' => 1,
                    'lastPage' => 1,
                    'perPage' => 14,
                    'total' => 2,
                ]);
            });
    }

    public function testGetSearchStoresWithoutParameter(): void
    {
        $this->storeFactory
            ->createOne();

        $this->get('/stores/search')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testGetSearchStoresWithUnmatchedSearch(): void
    {
        $this->storeFactory
            ->state(['name' => 'foo'])
            ->createOne();

        $this->get('/stores/search?q=bar')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data', [])
                ->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 0)
                    ->etc()));
    }

    public function testQueryStringTypes(): void
    {
        $this->get('/stores/search?q[]=')
            ->assertOk();
    }
}
