<?php

declare(strict_types=1);

use yii\data\Pagination;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\GroupUrlRule;
use yii\web\JsonParser;
use yii\web\JsonResponseFormatter;
use yii\web\Response;

return ArrayHelper::merge(require __DIR__.'/base.php', [
    'id' => 'web',
    'bootstrap' => [
        [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => '',
        ],
        'request' => [
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'response' => [
            'formatters' => [
                Response::FORMAT_JSON => [
                    'class' => JsonResponseFormatter::class,
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
                new GroupUrlRule([
                'prefix' => 'bookmarks',
                    'rules' => [
                        'countries' => 'countries',
                        'search' => 'search',
                        'tags' => 'tags',
                        '' => 'index',
                    ],
                ]),
                new GroupUrlRule([
                    'prefix' => 'labels',
                    'rules' => [
                        'countries' => 'countries',
                        'search' => 'search',
                        'tags' => 'tags',
                        '' => 'index',
                    ],
                ]),
                new GroupUrlRule([
                    'prefix' => 'playlists',
                    'rules' => [
                        '<id:[\w-]{11}>' => 'view',
                        '' => 'index',
                    ],
                ]),
                new GroupUrlRule([
                    'prefix' => 'stores',
                    'rules' => [
                        'countries' => 'countries',
                        'search' => 'search',
                        'tags' => 'tags',
                        '' => 'index',
                    ],
                ]),
                new GroupUrlRule([
                    'prefix' => 'tracks',
                    'rules' => [
                        'favorites' => 'favorites',
                        'genres' => 'genres',
                        'minimal-genres' => 'minimal-genres',
                        'search' => 'search',
                        '<id:[\w-]{11}>' => 'view',
                        '' => 'index',
                    ],
                ]),
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
    ],
    'container' => [
        'definitions' => [
            Pagination::class => [
                'pageSizeParam' => false,
            ],
        ],
    ],
    'params' => [
        'cors-origin' => explode(',', $_SERVER['CORS_ORIGIN']),
    ],
]);
