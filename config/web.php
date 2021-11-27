<?php

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
                'bookmarks/countries' => 'bookmark/countries',
                'bookmarks/search' => 'bookmark/search',
                'bookmarks/tags' => 'bookmark/tags',
                'bookmarks' => 'bookmark/index',

                'labels/countries' => 'label/countries',
                'labels/search' => 'label/search',
                'labels/tags' => 'label/tags',
                'labels' => 'label/index',

                'playlists/<id:[\w-]{11}>' => 'playlist/view',
                'playlists' => 'playlist/index',

                'stores/countries' => 'store/countries',
                'stores/search' => 'store/search',
                'stores/tags' => 'store/tags',
                'stores' => 'store/index',

                'tracks/favorites' => 'track/favorites',
                'tracks/genres' => 'track/genres',
                'tracks/minimal-genres' => 'track/minimal-genres',
                'tracks/search' => 'track/search',
                'tracks/<id:[\w-]{11}>' => 'track/view',
                'tracks' => 'track/index',
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
        'cors-origin' => explode(',', $_SERVER['CORS_ORIGIN']),
    ],
]);
