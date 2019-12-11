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

use app\resources\Playlist;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class PlaylistController extends Controller
{
    public function actionIndex(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Playlist::find()->latest(),
            'pagination' => false,
        ]);
    }

    public function actionView(string $id): Playlist
    {
        $model = Playlist::findOne(Yii::$app->hashids->decode($id));

        if (null === $model) {
            throw new NotFoundHttpException('Page not found.');
        }

        return $model;
    }
}
