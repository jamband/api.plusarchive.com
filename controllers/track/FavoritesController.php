<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\controllers\Controller;
use app\models\Track;
use yii\data\ActiveDataProvider;

/**
 * @noinspection PhpUnused
 */
class FavoritesController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Track::find()->favoritesInLatestOrder(),
        ]);
    }
}
