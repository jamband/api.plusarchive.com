<?php

declare(strict_types=1);

namespace App\Providers;

use Hashids\Hashids;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class HashidsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            Hashids::class,
            fn (Application $app): Hashids => new Hashids(
                salt: $app['config']['hashids.salt'],
                minHashLength: $app['config']['hashids.minHashLength'],
                alphabet: $app['config']['hashids.alphabet'],
            ),
        );
    }
}
