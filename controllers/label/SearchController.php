<?php

declare(strict_types=1);

namespace app\controllers\label;

use app\models\Label;
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
            'query' => Label::find()->searchInNameOrder($q),
            'pagination' => [
                'pageSize' => self::PER_PAGE,
            ],
        ]);
    }
}
