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

return yii\helpers\ArrayHelper::merge(require __DIR__.'/base.php', [
    'id' => 'web',
    'components' => [
        'user' => [
            'identityClass' => '',
        ],
        'request' => [
            'parsers' => [
                'application/json' => yii\web\JsonParser::class,
            ],
        ],
        'response' => [
            'formatters' => [
                yii\web\Response::FORMAT_JSON => [
                    'class' => yii\web\JsonResponseFormatter::class,
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'tracks/genres' => 'track/genres',
                'tracks/favorites' => 'track/favorites',
                'tracks/minimal-genres' => 'track/minimal-genres',
                '<controller:(track|playlist|label|store|bookmark)>s' => '<controller>/index',
                '<controller:(label|store|bookmark)>s/countries' => '<controller>/countries',
                '<controller:(label|store|bookmark)>s/tags' => '<controller>/tags',
                '<controller:(track|playlist)>s/<id:[\w-]+>' => '<controller>/view',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
    ],
    'container' => [
        'definitions' => [
            yii\data\Pagination::class => [
                'pageSizeParam' => false,
            ],
        ],
    ],
    'params' => [
        'cors-origin' => $_SERVER['CORS_ORIGIN'],
    ],
]);
