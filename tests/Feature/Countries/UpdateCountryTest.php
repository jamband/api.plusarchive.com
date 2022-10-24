<?php

declare(strict_types=1);

namespace Tests\Feature\Countries;

use App\Groups\Countries\Country;
use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class UpdateCountryTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('PUT /countries/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('PUT /countries/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/countries/1', [
                'name' => 'foo',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdateCountry(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Country::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->putJson('/countries/'.$country->id, [
                'name' => 'updated_country1',
            ])
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', $country->id);
                $json->where('name', 'updated_country1');
            });

        $this->assertDatabaseCount(Country::class, 1);

        $this->assertDatabaseHas(Country::class, [
            'id' => $country->id,
            'name' => 'updated_country1',
        ]);
    }
}
