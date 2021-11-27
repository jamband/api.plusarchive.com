<?php

declare(strict_types=1);

use yii\console\Application;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$basePath = dirname(__DIR__, 2);
require $basePath.'/vendor/autoload.php';
require $basePath.'/vendor/yiisoft/yii2/Yii.php';

new Application([
    'id' => 'test-console',
    'basePath' => $basePath,
    'controllerNamespace' => 'app\commands',
    'enableCoreCommands' => false,
]);
