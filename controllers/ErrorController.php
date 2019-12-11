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

class ErrorController extends Controller
{
    public function actionIndex(): array
    {
        $exception = Yii::$app->errorHandler->exception;

        if (null !== $exception) {
            $data['message'] = $exception->getMessage();
            $data['statusCode'] = $exception->statusCode;

            return $data;
        }

        return [];
    }
}
