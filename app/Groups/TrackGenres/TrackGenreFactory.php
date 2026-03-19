<?php

declare(strict_types=1);

namespace App\Groups\TrackGenres;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrackGenre>
 */
#[UseModel(TrackGenre::class)]
class TrackGenreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('genre####'),
        ];
    }
}
