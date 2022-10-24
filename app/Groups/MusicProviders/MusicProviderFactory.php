<?php

declare(strict_types=1);

namespace App\Groups\MusicProviders;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MusicProvider>
 */
class MusicProviderFactory extends Factory
{
    protected $model = MusicProvider::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('provider####'),
        ];
    }
}
