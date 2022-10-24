<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())->setRules([
    '@PSR12' => true,
])->setFinder(PhpCsFixer\Finder::create()->in([
    __DIR__.'/app',
    __DIR__.'/config',
    __DIR__.'/database',
    __DIR__.'/lang',
    __DIR__.'/public',
    __DIR__.'/tests',
]));
