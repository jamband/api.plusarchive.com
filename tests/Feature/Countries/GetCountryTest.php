<?php

declare(strict_types=1);

namespace Tests\Feature\Countries;

use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class GetCountryTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('GET /countries/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('GET /countries/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/countries/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testGetCountry(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->actingAs(UserFactory::new()->makeOne())
            ->getJson('/countries/'.$country->id)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) use ($country) {
                $json->where('id', $country->id)
                    ->where('name', $country->name);
            });
    }
}
