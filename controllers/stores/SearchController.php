<?php

declare(strict_types=1);

namespace app\controllers\stores;

use app\models\Store;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(string $q): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Store::find()->searchInNameOrder($q),
            'pagination' => [
                'pageSize' => self::PER_PAGE,
            ],
        ]);
    }
}
