<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;

return Application::configure(dirname(__DIR__))
    ->withRouting(require __DIR__.'/routes.php')
    ->withMiddleware(require __DIR__.'/middleware.php')
    ->withExceptions(require __DIR__.'/exceptions.php')
    ->create();
