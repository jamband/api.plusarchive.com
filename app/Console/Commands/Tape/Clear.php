<?php

declare(strict_types=1);

namespace App\Console\Commands\Tape;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;

class Clear extends Command
{
    protected $signature = 'tape:clear';

    protected $description = 'Delete all tapes';

    public function handle(Filesystem $filesystem): int
    {
        /** @var Application $app */
        $app = $this->laravel;

        foreach ($filesystem->files($app->storagePath('app/tapes')) as $file) {
            if ('.gitignore' !== $file->getFilename()) {
                $filesystem->delete($file->getPathname());
            }
        }

        $this->info('All tapes has been deleted.');

        return self::SUCCESS;
    }
}
