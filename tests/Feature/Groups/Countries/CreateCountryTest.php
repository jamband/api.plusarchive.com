<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Countries;

use App\Groups\Countries\Country;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\TestMiddleware;

class CreateCountryTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('POST /countries');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('POST /countries');
    }

    public function testCreateCountry(): void
    {
        $this->assertDatabaseCount(Country::class, 0);

        $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/countries', [
                'name' => 'country1',
            ])
            ->assertCreated()
            ->assertHeader(
                'Location',
                $this->app['config']['app.url'].'/countries/1'
            )->assertJson(fn (AssertableJson $json) => $json
                ->where('id', 1)
                ->where('name', 'country1'));

        $this->assertDatabaseCount(Country::class, 1);

        $this->assertDatabaseHas(Country::class, [
            'id' => 1,
            'name' => 'country1',
        ]);
    }
}
