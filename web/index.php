<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use yii\web\Application;

if (preg_match('/\A(127.0.0.1|localhost|dev.api.plusarchive)\z/', $_SERVER['SERVER_NAME'])) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
}

require __DIR__.'/../vendor/yiisoft/yii2/Yii.php';
require __DIR__.'/../vendor/autoload.php';

if (defined('YII_DEBUG')) {
    Dotenv::createImmutable(dirname(__DIR__))->load();
}

(new Application(require __DIR__.'/../config/web.php'))->run();
