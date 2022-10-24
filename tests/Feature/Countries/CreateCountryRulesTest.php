<?php

declare(strict_types=1);

namespace Tests\Feature\Countries;

use App\Groups\Countries\CountryFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreateCountryRulesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data = []): TestResponse
    {
        return $this->actingAs(UserFactory::new()->makeOne())
            ->postJson('/countries', $data)
            ->assertUnprocessable();
    }

    public function testNameRequiredRule(): void
    {
        $this->request()
            ->assertJsonPath('errors.name', __('validation.required', [
                'attribute' => 'name',
            ]));
    }

    public function testNameStringRule(): void
    {
        $this->request(['name' => 1])
            ->assertJsonPath('errors.name', __('validation.string', [
                'attribute' => 'name',
            ]));
    }

    public function testNameMaxStringRule(): void
    {
        $this->request(['name' => str_repeat('a', 101)])
            ->assertJsonPath('errors.name', __('validation.max.string', [
                'attribute' => 'name',
                'max' => 100,
            ]));
    }

    public function testNameUniqueRule(): void
    {
        $country = CountryFactory::new()
            ->createOne();

        $this->request(['name' => $country->name])
            ->assertJsonPath('errors.name', __('validation.unique', [
                'attribute' => 'name',
            ]));
    }
}
