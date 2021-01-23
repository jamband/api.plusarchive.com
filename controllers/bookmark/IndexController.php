<?php

/*
 * This file is part of the api.plusarchive.com
 *
 * (c) Tomoki Morita <tmsongbooks215@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace app\controllers\bookmark;

use app\controllers\Controller;
use app\models\Bookmark;
use app\models\BookmarkTag;
use yii\data\ActiveDataProvider;

/**
 * @noinspection PhpUnused
 */
class IndexController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(?string $country = null, ?string $tag = null, ?string $search = null): ActiveDataProvider
    {
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

        if (null !== $search) {
            $query->searchInNameOrder($search);
        } else {
            $query->latest();
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);
    }
}
