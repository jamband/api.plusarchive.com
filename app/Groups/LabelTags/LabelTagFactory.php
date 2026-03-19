<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabelTag>
 */
#[UseModel(LabelTag::class)]
class LabelTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('tag####'),
        ];
    }
}
