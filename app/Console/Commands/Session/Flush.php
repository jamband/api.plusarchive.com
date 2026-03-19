<?php

declare(strict_types=1);

namespace App\Console\Commands\Session;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;

#[Signature('session:flush')]
#[Description('Flush all sessions')]
class Flush extends Command
{
    public function handle(Filesystem $filesystem): int
    {
        /** @var Application $app */
        $app = $this->laravel;

        if ('file' === $app['config']['session.driver']) {
            foreach ($filesystem->files($app['config']['session.files']) as $file) {
                if ('.gitignore' !== $file->getFilename()) {
                    $filesystem->delete($file->getPathname());
                }
            }

            $this->info('All sessions has been flush.');

            return self::SUCCESS;
        }

        $this->warn('Currently flush session only if the session driver is set to "file".');

        return self::FAILURE;
    }
}
