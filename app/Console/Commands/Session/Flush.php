<?php

declare(strict_types=1);

namespace App\Console\Commands\Session;

use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class Flush extends Command
{
    protected $signature = 'session:flush';

    protected $description = 'Flush all sessions';

    public function handle(
        Config $config,
        Filesystem $filesystem,
    ): int {
        if ('file' === $config->get('session.driver')) {
            foreach ($filesystem->files($config->get('session.files')) as $file) {
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
