<?php

declare(strict_types=1);

namespace app\controllers\label;

use app\models\Label;
use app\models\LabelTag;
use yii\data\ActiveDataProvider;

class IndexController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(
        string|null $country = null,
        string|null $tag = null,
    ): ActiveDataProvider {
        $query = Label::find();

        if (null !== $country) {
            $query->country($country);
        }

        if (null !== $tag) {
            if (LabelTag::hasName($tag)) {
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
