<?php

declare(strict_types=1);

namespace app\controllers\playlist;

use app\controllers\Controller;
use app\models\Playlist;
use yii\data\ActiveDataProvider;

class IndexController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Playlist::find()->latest(),
            'pagination' => false,
        ]);
    }
}
