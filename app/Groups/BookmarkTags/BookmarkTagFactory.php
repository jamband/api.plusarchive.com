<?php

declare(strict_types=1);

namespace App\Groups\BookmarkTags;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookmarkTag>
 */
class BookmarkTagFactory extends Factory
{
    protected $model = BookmarkTag::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->numerify('tag####'),
        ];
    }
}
