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
require __DIR__.'/../../vendor/yiisoft/yii2/Yii.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$baseConfig = require __DIR__.'/../../config/web.php';
new yii\web\Application(yii\helpers\ArrayHelper::merge($baseConfig, [
    'id' => 'test-feature',
    'components' => [
        'db' => null,
        'hashids' => [
            'salt' => 'test',
        ],
    ],
]));
