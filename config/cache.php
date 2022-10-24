<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [
    'default' => 'apc',
    'stores' => [
        'apc' => [
            'driver' => 'apc',
        ],
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
    ],
    'prefix' => Str::slug(env('APP_NAME', 'plusarchive'), '_').'_cache_',
];
