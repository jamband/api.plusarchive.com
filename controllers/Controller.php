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

namespace app\controllers;

use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\rest\Serializer;
use yii\web\Controller as BaseController;
use yii\web\Response;

class Controller extends BaseController
{
    public array $serializer = [
        'class' => Serializer::class,
        'collectionEnvelope' => 'items',
    ];

    public $enableCsrfValidation = false;

    protected array $verbs = [];

    public function behaviors(): array
    {
        return [
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => [Yii::$app->params['cors-origin']],
                    'Access-Control-Request-Method' => ['GET'],
                    'Access-Control-Request-Headers' => ['Content-Type'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => ['index' => $this->verbs],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'rateLimiter' => [
                'class' => RateLimiter::class,
                'enableRateLimitHeaders' => false,
            ],
        ];
    }

    public function afterAction($action, $result)
    {
        return Yii::createObject($this->serializer)->serialize(
            parent::afterAction($action, $result)
        );
    }
}
