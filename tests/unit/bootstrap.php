<?php

declare(strict_types=1);

use app\components\Hashids;
use yii\web\Application;

require __DIR__.'/../bootstrap.php';

new Application([
    'id' => 'test',
    'basePath' => dirname(__DIR__, 2),
    'components' => [
        'formatter' => [
            'dateFormat' => 'yyyy.MM.dd',
            'datetimeFormat' => 'yyyy.MM.dd HH:mm',
        ],
        'hashids' => [
            'class' => Hashids::class,
            'salt' => 'test',
            'minHashLength' => 11,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-',
        ],
    ],
]);
