<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Countries;

use App\Groups\Countries\Country;
use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    private Country $country;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = new Country();
    }

    public function testTimestamps(): void
    {
        $this->assertFalse($this->country->timestamps);
    }

    public function testGetIdByName(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->assertSame(null, $this->country->getIdByName('foo'));
        $this->assertSame(1, $this->country->getIdByName($country->name));
    }

    public function testGetNames(): void
    {
        CountryFactory::new()
            ->count(5)
            ->state(new Sequence(
                ['name' => 'Unknown'],
                ['name' => 'Worldwide'],
                ['name' => 'Foo'],
                ['name' => 'Bar'],
                ['name' => 'Baz'],
            ))
            ->create();

        $this->assertSame(
            ['Unknown', 'Worldwide', 'Bar', 'Baz', 'Foo'],
            $this->country->getNames()
        );
    }
}
