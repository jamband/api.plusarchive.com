<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\models\Track;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(string $q): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Track::find()->searchInTitleOrder($q),
            'pagination' => [
                'pageSize' => self::PER_PAGE,
            ],
        ]);
    }
}
