<?php

declare(strict_types=1);

use yii\web\Application;

require __DIR__.'/../bootstrap.php';

new Application([
    'id' => 'test',
    'basePath' => dirname(__DIR__, 2),
    'components' => [
        'formatter' => require __DIR__.'/../config/formatter.php',
        'hashids' => require __DIR__.'/../config/hashids.php',
    ],
]);
