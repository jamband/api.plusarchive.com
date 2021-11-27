<?php

declare(strict_types=1);

namespace app\controllers\bookmark;

use app\models\Bookmark;
use app\models\BookmarkTag;
use yii\data\ActiveDataProvider;

/**
 * @noinspection PhpUnused
 */
class IndexController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(
        string|null $country = null,
        string|null $tag = null,
    ): ActiveDataProvider {
        $query = Bookmark::find();

        if (null !== $country) {
            $query->country($country);
        }

        if (null !== $tag) {
            if (BookmarkTag::hasName($tag)) {
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
