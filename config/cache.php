<?php

declare(strict_types=1);

use Illuminate\Support\Str;

$appName = env('APP_NAME', 'plusarchive');
assert(is_string($appName));

return [
    'default' => env('CACHE_STORE', 'apc'),
    'stores' => [
        'apc' => [
            'driver' => 'apc',
        ],
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
    ],
    'prefix' => env('CACHE_PREFIX', Str::slug($appName, '_').'_cache_'),
];
