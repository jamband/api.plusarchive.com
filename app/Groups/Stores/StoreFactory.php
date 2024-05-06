<?php

declare(strict_types=1);

namespace App\Groups\Stores;

use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        $countryFactory = new CountryFactory();

        return [
            'name' => $this->faker->unique()->numerify('store####'),
            'country_id' => $countryFactory,
            'url' => $this->faker->url(),
            'links' => $this->faker->url()."\n".$this->faker->url(),
        ];
    }
}
