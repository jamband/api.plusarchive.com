<?php

declare(strict_types=1);

use yii\console\Application;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../../vendor/yiisoft/yii2/Yii.php';

new Application([
    'id' => 'test-console',
    'basePath' => dirname(__DIR__, 2),
    'controllerNamespace' => 'app\commands',
    'enableCoreCommands' => false,
]);
