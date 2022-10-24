<?php

declare(strict_types=1);

namespace App\Groups\Countries;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('country####'),
        ];
    }
}
