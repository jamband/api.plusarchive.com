<?php

declare(strict_types=1);

namespace App\Groups\Users;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public const PASSWORD = 'password';

    protected $model = User::class;

    protected static string|null $password;


    public function definition(): array
    {
        /** @var HashManager $hash */
        $hash = Container::getInstance()->make(HashManager::class);
        $carbon = new Carbon();

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => $carbon,
            'password' => static::$password ??= $hash->make(self::PASSWORD),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * @return Factory<User>
     */
    public function unverified(): Factory
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
