<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrackGenre>
 */
class TrackGenreFactory extends Factory
{
    protected $model = TrackGenre::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('genre####'),
        ];
    }
}
