<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Foundation\Application;
use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider;

class DatabaseQueryLogServiceProvider extends ServiceProvider
{
    public function boot(
        Application $app,
        Connection $db,
        LogManager $log,
    ): void {
        if ($app->isLocal()) {
            $db->listen(function ($query) use ($log) {
                $log->channel('query')->debug(
                    '[time: '.$query->time.'ms] '.$query->sql
                );
            });
        }
    }
}
