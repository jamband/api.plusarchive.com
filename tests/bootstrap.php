<?php

declare(strict_types=1);

use Dotenv\Dotenv;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../vendor/yiisoft/yii2/Yii.php';

Dotenv::createImmutable(dirname(__DIR__))->load();
