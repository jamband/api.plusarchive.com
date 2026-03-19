<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookmarkTag>
 */
#[UseModel(BookmarkTag::class)]
class BookmarkTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('tag####'),
        ];
    }
}
