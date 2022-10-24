<?php

declare(strict_types=1);

namespace App\Groups\LabelTags;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabelTag>
 */
class LabelTagFactory extends Factory
{
    protected $model = LabelTag::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('tag####'),
        ];
    }
}
