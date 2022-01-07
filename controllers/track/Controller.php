<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\controllers\Controller as BaseController;
use app\models\Track;
use Yii;
use yii\web\NotFoundHttpException;

class Controller extends BaseController
{
    protected const PER_PAGE = 24;

    protected function findModel(string $id): Track
    {
        $model = Track::findOne(
            Yii::$app->hashids->decode($id)
        );

        if (null === $model) {
            throw new NotFoundHttpException('Not Found.');
        }

        return $model;
    }
}
