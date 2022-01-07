<?php

declare(strict_types=1);

namespace app\controllers;

use app\filters\AccessControl;
use app\rest\Serializer;
use Yii;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Controller as BaseController;
use yii\web\HttpException;

class Controller extends BaseController
{
    public array $serializer = [
        'class' => Serializer::class,
    ];

    protected array $roles = [];
    protected array $verbs = [];

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => $this->roles]],
            ],
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => Yii::$app->params['cors-origin'],
                    'Access-Control-Request-Method' => ['GET', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['Content-Type', $this->request::CSRF_HEADER],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => ['index' => $this->verbs],
            ],
        ];
    }

    public function beforeAction($action): bool
    {
        if (BaseController::beforeAction($action)) {
            if (
                $this->enableCsrfValidation &&
                Yii::$app->errorHandler->exception === null &&
                !$this->request->validateCsrfToken()
            ) {
                throw new HttpException(419);
            }

            return true;
        }

        return false;
    }

    public function afterAction($action, $result): mixed
    {
        return Yii::createObject($this->serializer)->serialize(
            parent::afterAction($action, $result)
        );
    }
}
