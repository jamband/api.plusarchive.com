<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Stores;

use App\Groups\Stores\Store;
use App\Groups\Stores\StoreFactory;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetAdminStoresTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private StoreFactory $storeFactory;
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->storeFactory = new StoreFactory();
        $this->carbon = new Carbon();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/stores/admin')
            ->assertConflict();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/stores/admin')
            ->assertUnauthorized();
    }

    public function testGetAdminStores(): void
    {
        /** @var array<int, Store> $stores */
        $stores = $this->storeFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index + 1),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/stores/admin')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->where('data.0', [
                    'id' => $stores[1]->id,
                    'name' => $stores[1]->name,
                    'country' => $stores[1]->country->name,
                    'url' => $stores[1]->url,
                    'links' => $stores[1]->links,
                    'tags' => [],
                    'created_at' => $stores[1]->created_at->format('Y-m-d H:i'),
                    'updated_at' => $stores[1]->updated_at->format('Y-m-d H:i'),
                ]);

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminStoresWithSortAsc(): void
    {
        /** @var array<int, Store> $stores */
        $stores = $this->storeFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/stores/admin?sort=name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $stores[0]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminStoresWithSortDesc(): void
    {
        /** @var array<int, Store> $stores */
        $stores = $this->storeFactory
            ->count(2)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/stores/admin?sort=-name')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $stores[1]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testGetAdminStoresWithName(): void
    {
        /** @var array<int, Store> $stores */
        $stores = $this->storeFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => ($this->carbon::now())->addMinutes($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->userFactory->makeOne())
            ->get('/stores/admin?name=ba')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson(function (AssertableJson $json) use ($stores) {
                $json->has('data.0', fn (AssertableJson $json) => $json
                    ->where('id', $stores[2]->id)
                    ->etc());

                $json->has('pagination', fn (AssertableJson $json) => $json
                    ->where('total', 2)
                    ->etc());
            });
    }

    public function testQueryStringTypes(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->get('/stores/admin?name[]=&country[]=&tag[]=&sort[]=')
            ->assertOk();
    }
}
