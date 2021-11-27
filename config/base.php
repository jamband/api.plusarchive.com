<?php

declare(strict_types=1);

return [
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => $_SERVER['DB_DSN'],
            'username' => $_SERVER['DB_USER'],
            'password' => $_SERVER['DB_PASS'],
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'formatter' => [
            'dateFormat' => 'yyyy.MM.dd',
            'datetimeFormat' => 'yyyy.MM.dd HH:mm',
        ],
        'cache' => [
            'class' => yii\caching\ApcCache::class,
            'useApcu' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_GET'],
                ],
            ],
        ],
        'hashids' => [
            'class' => app\components\Hashids::class,
            'salt' => $_SERVER['HASHIDS_SALT'],
            'minHashLength' => 11,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-',
        ],
    ],
];
