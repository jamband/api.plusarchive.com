<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreTag>
 */
#[UseModel(StoreTag::class)]
class StoreTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('tag####'),
        ];
    }
}
