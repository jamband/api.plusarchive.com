<?php

declare(strict_types=1);

namespace app\controllers\playlists;

use app\controllers\Controller;
use app\models\Playlist;
use Yii;
use yii\web\NotFoundHttpException;

class ViewController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(string $id): Playlist
    {
        $model = Playlist::findOne(
            Yii::$app->hashids->decode($id)
        );

        if (null === $model) {
            throw new NotFoundHttpException('Not Found.');
        }

        return $model;
    }
}
