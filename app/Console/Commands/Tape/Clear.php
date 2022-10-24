<?php

declare(strict_types=1);

namespace App\Console\Commands\Tape;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Clear extends Command
{
    protected $signature = 'tape:clear';

    protected $description = 'Delete all tapes';

    public function handle(
        Filesystem $filesystem,
    ): int {
        foreach ($filesystem->files($this->laravel->storagePath('app/tapes')) as $file) {
            if ('.gitignore' !== $file->getFilename()) {
                $filesystem->delete($file->getPathname());
            }
        }

        $this->info('All tapes has been deleted.');

        return self::SUCCESS;
    }
}
