<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

class ErrorController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): array
    {
        $exception = Yii::$app->errorHandler->exception;

        if ($exception instanceof NotFoundHttpException) {
            $data['message'] = 'Not Found';
        }

        if ($exception instanceof  MethodNotAllowedHttpException) {
            $data['message'] = 'Method Not Allowed';
        }

        return $data ?? [];
    }
}
