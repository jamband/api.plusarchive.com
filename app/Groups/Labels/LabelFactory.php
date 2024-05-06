<?php

declare(strict_types=1);

namespace App\Groups\Labels;

use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Label>
 */
class LabelFactory extends Factory
{
    protected $model = Label::class;

    public function definition(): array
    {
        $countryFactory = new CountryFactory();

        return [
            'name' => $this->faker->unique()->numerify('label####'),
            'country_id' => $countryFactory,
            'url' => $this->faker->url(),
            'links' => $this->faker->url()."\n".$this->faker->url(),
        ];
    }
}
