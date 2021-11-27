<?php

declare(strict_types=1);

namespace app\controllers\store;

use app\models\Store;
use yii\data\ActiveDataProvider;

/**
 * @noinspection PhpUnused
 */
class SearchController extends Controller
{
    protected array $verbs = ['GET'];

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
