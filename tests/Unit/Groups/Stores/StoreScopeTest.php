<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Stores;

use App\Groups\Countries\CountryFactory;
use App\Groups\Stores\Store;
use App\Groups\Stores\StoreFactory;
use App\Groups\StoreTags\StoreTagFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreScopeTest extends TestCase
{
    use RefreshDatabase;

    private Store $store;
    private StoreFactory $storeFactory;
    private StoreTagFactory $tagFactory;
    private CountryFactory $countryFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = new Store();
        $this->storeFactory = new StoreFactory();
        $this->tagFactory = new StoreTagFactory();
        $this->countryFactory = new CountryFactory();
    }

    public function testScopeOfCountry(): void
    {
        $this->storeFactory
            ->count(1)
            ->for(
                $this->countryFactory
                    ->state(['name' => 'foo'])
            )
            ->create();

        $this->storeFactory
            ->count(2)
            ->for(
                $this->countryFactory
                    ->state(['name' => 'bar'])
            )
            ->create();

        $this->assertSame(0, $this->store->ofCountry('')->count());
        $this->assertSame(1, $this->store->ofCountry('foo')->count());
        $this->assertSame(2, $this->store->ofCountry('bar')->count());
        $this->assertSame(0, $this->store->ofCountry('baz')->count());
    }

    public function testScopeOfTag(): void
    {
        /** @var array<int, Store> $stores */
        $stores = $this->storeFactory
            ->count(2)
            ->create();

        $this->tagFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $stores[0]->tags()->sync([1, 2]);
        $stores[1]->tags()->sync([2]);

        $this->assertSame(0, $this->store->ofTag('')->count());
        $this->assertSame(1, $this->store->ofTag('foo')->count());
        $this->assertSame(2, $this->store->ofTag('bar')->count());
        $this->assertSame(0, $this->store->ofTag('baz')->count());
    }

    public function testScopeOfSearch(): void
    {
        $this->storeFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(0, $this->store->ofSearch('')->count());
        $this->assertSame(1, $this->store->ofSearch('o')->count());
        $this->assertSame(2, $this->store->ofSearch('ba')->count());
        $this->assertSame(0, $this->store->ofSearch('qux')->count());
    }

    public function testScopeInNameOrder(): void
    {
        $this->storeFactory
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(
            ['bar', 'baz', 'foo'],
            $this->store->inNameOrder()->pluck('name')->toArray()
        );
    }
}
