<?php

declare(strict_types=1);

namespace App\Groups\StoreTags;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreTag>
 */
class StoreTagFactory extends Factory
{
    protected $model = StoreTag::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('tag####'),
        ];
    }
}
