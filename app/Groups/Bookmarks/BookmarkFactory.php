<?php

declare(strict_types=1);

namespace App\Groups\Bookmarks;

use App\Groups\Countries\CountryFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bookmark>
 */
class BookmarkFactory extends Factory
{
    protected $model = Bookmark::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('bookmark####'),
            'country_id' => CountryFactory::new(),
            'url' => $this->faker->url(),
            'links' => $this->faker->url()."\n".$this->faker->url(),
        ];
    }
}
