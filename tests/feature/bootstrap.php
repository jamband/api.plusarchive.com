<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use yii\helpers\ArrayHelper;
use yii\web\Application;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$basePath = dirname(__DIR__, 2);
require $basePath.'/vendor/autoload.php';
require $basePath.'/vendor/yiisoft/yii2/Yii.php';

$dotenv = Dotenv::createImmutable($basePath);
$dotenv->load();

$baseConfig = require $basePath.'/config/web.php';
new Application(ArrayHelper::merge($baseConfig, [
    'id' => 'test-feature',
    'components' => [
        'db' => null,
        'hashids' => [
            'salt' => 'test',
        ],
    ],
]));
