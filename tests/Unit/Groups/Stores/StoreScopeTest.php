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

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = new Store();
    }

    public function testScopeOfCountry(): void
    {
        StoreFactory::new()
            ->count(1)
            ->for(
                CountryFactory::new()
                    ->state(['name' => 'foo'])
            )
            ->create();

        StoreFactory::new()
            ->count(2)
            ->for(
                CountryFactory::new()
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
        $stores = StoreFactory::new()
            ->count(2)
            ->create();

        StoreTagFactory::new()
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
        StoreFactory::new()
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
        StoreFactory::new()
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
