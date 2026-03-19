<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MusicProvider>
 */
#[UseModel(MusicProvider::class)]
class MusicProviderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('provider####'),
        ];
    }
}
