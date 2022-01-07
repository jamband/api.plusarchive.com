<?php

declare(strict_types=1);

namespace app\controllers\store;

use app\models\Store;
use app\models\StoreTag;
use yii\data\ActiveDataProvider;

class IndexController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(
        string|null $country = null,
        string|null $tag = null,
    ): ActiveDataProvider {
        $query = Store::find();

        if (null !== $country) {
            $query->country($country);
        }

        if (null !== $tag) {
            if (StoreTag::hasName($tag)) {
                $query->allTagValues($tag);
            } else {
                $query->nothing();
            }
        }

        return new ActiveDataProvider([
            'query' => $query->latest(),
            'pagination' => [
                'pageSize' => self::PER_PAGE,
            ],
        ]);
    }
}
