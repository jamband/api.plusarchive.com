<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\controllers\Controller;
use app\models\Track;
use yii\data\ActiveDataProvider;

class FavoritesController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Track::find()->favoritesInLatestOrder(),
        ]);
    }
}
