<?php

declare(strict_types=1);

namespace App\Console\Commands\Development;

use Illuminate\Console\Command;

class Clean extends Command
{
    protected $signature = 'dev:clean';

    protected $description = 'Clean up development environment';

    public function handle(): int
    {
        $this->call('session:flush');
        $this->call('tape:clear');
        $this->call('optimize:clear');

        $files = [
            '.php-cs-fixer.cache',
            '.phpunit.result.cache',
            'storage/logs/laravel.log',
            'storage/logs/query.log',
        ];

        foreach ($files as $file) {
            if (file_exists($file) && unlink($file)) {
                $this->info('Deleted: '.$file);
            }
        }

        $this->info('Clean up completed.');

        return self::SUCCESS;
    }
}
