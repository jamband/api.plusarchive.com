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
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * @noinspection PhpUnused
 */
class ErrorController extends Controller
{
    public function actionIndex(): array
    {
        $exception = Yii::$app->errorHandler->exception;

        if ($exception instanceof NotFoundHttpException) {
            $data['message'] = 'Not found.';
            $data['status'] = $exception->statusCode;
        }

        if ($exception instanceof  MethodNotAllowedHttpException) {
            $data['message'] = $exception->getMessage();
            $data['status'] = $exception->statusCode;
        }

        return $data ?? [];
    }
}
