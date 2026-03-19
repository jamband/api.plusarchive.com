<?php

declare(strict_types=1);

namespace App\Console\Commands\Tape;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;

#[Signature('tape:clear')]
#[Description('Delete all tapes')]
class Clear extends Command
{
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
