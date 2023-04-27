<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\DatabaseQueryLogServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\HashidsServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'plusarchive'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool)env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'providers' => ServiceProvider::defaultProviders()->except([
        RedisServiceProvider::class,
    ])->merge([
        AppServiceProvider::class,
        AuthServiceProvider::class,
        DatabaseQueryLogServiceProvider::class,
        EventServiceProvider::class,
        RouteServiceProvider::class,
        HashidsServiceProvider::class,
    ])->toArray(),
];
