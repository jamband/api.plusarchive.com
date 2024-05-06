<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\DatabaseQueryLogServiceProvider;
use App\Providers\HashidsServiceProvider;

return [
    AppServiceProvider::class,
    DatabaseQueryLogServiceProvider::class,
    HashidsServiceProvider::class,
];
