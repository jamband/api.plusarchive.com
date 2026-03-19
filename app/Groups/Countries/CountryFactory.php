<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
#[UseModel(Country::class)]
class CountryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('country####'),
        ];
    }
}
