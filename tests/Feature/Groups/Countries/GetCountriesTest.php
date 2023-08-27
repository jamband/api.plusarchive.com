<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Countries;

use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetCountriesTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /countries');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /countries');
    }

    public function testGetCountries(): void
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

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/countries')
            ->assertOk()
            ->assertExactJson(['Unknown', 'Worldwide', 'Bar', 'Baz', 'Foo']);
    }
}
