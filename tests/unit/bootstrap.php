<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\web\Application;

require __DIR__.'/../bootstrap.php';

new Application(ArrayHelper::merge(require __DIR__.'/../../config/base.php', [
    'id' => 'test',
    'components' => [
        'db' => null,
        'hashids' => [
            'salt' => 'test',
        ],
    ],
]));
