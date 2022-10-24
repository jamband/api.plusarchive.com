<?php

declare(strict_types=1);

namespace Tests\Feature\Countries;

use App\Groups\Countries\Country;
use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestMiddleware;

class DeleteCountryTest extends TestCase
{
    use RefreshDatabase;
    use TestMiddleware;

    public function testVerifiedMiddleware(): void
    {
        $this->assertVerifiedMiddleware('DELETE /countries/1');
    }

    public function testAuthMiddleware(): void
    {
        $this->assertAuthMiddleware('DELETE /countries/1');
    }

    public function testModelNotFound(): void
    {
        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/countries/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testDeleteCountry(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->assertDatabaseCount(Country::class, 1);

        $this->actingAs(UserFactory::new()->makeOne())
            ->deleteJson('/countries/'.$country->id)
            ->assertNoContent();

        $this->assertDatabaseCount(Country::class, 0);
    }
}
