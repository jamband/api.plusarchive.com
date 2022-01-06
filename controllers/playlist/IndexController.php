<?php

declare(strict_types=1);

namespace app\controllers\playlist;

use app\controllers\Controller;
use app\models\Playlist;
use yii\data\ActiveDataProvider;

class IndexController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Playlist::find()->latest(),
            'pagination' => false,
        ]);
    }
}
