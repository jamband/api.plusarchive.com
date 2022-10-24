<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Countries;

use App\Groups\Countries\Country;
use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryScopeTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = new Country();
    }

    public function testScopeSearch(): void
    {
        CountryFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        $this->assertSame(0, $this->country->ofSearch('')->count());
        $this->assertSame(1, $this->country->ofSearch('o')->count());
        $this->assertSame(2, $this->country->ofSearch('ba')->count());
        $this->assertSame(0, $this->country->ofSearch('qux')->count());
    }

    public function testScopeInNameOrder(): void
    {
        CountryFactory::new()
            ->count(3)
            ->state(new Sequence(
                ['name' => 'foo'],
                ['name' => 'bar'],
                ['name' => 'baz'],
            ))
            ->create();

        /** @var array<int, Country> $countries */
        $countries = $this->country->inNameOrder()->get();

        $this->assertSame('bar', $countries[0]->name);
        $this->assertSame('baz', $countries[1]->name);
        $this->assertSame('foo', $countries[2]->name);
    }
}
