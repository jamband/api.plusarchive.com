<?php

declare(strict_types=1);

use yii\console\Application;

require __DIR__.'/../bootstrap.php';

new Application([
    'id' => 'test',
    'basePath' => dirname(__DIR__, 2),
    'controllerNamespace' => 'app\commands',
    'enableCoreCommands' => false,
]);
