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

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/yiisoft/yii2/Yii.php';

new yii\console\Application([
    'id' => 'test-console',
    'basePath' => dirname(__DIR__, 2),
    'controllerNamespace' => 'app\commands',
    'enableCoreCommands' => false,
]);
