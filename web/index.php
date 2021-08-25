<?php

/*
 * This file is part of the api.plusarchive.com
 *
 * (c) Tomoki Morita <tmsongbooks215@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Dotenv\Dotenv;
use yii\web\Application;

$isLocal = (bool)preg_match(
    '/\A(127.0.0.1|localhost|dev.api.plusarchive)\z/',
    $_SERVER['SERVER_NAME']
);

if ($isLocal) {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    defined('YII_ENV') or define('YII_ENV', 'dev');
}

$basePath = dirname(__DIR__);
require $basePath.'/vendor/yiisoft/yii2/Yii.php';
require $basePath.'/vendor/autoload.php';

if ($isLocal) {
    $dotenv = Dotenv::createImmutable($basePath);
    $dotenv->load();
}

$config = require $basePath.'/config/web.php';
(new Application($config))->run();
