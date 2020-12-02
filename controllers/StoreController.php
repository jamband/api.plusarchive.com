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

namespace app\controllers;

use app\resources\Store;
use app\resources\StoreTag;
use yii\data\ActiveDataProvider;

class StoreController extends Controller
{
    public function actionIndex(?string $country = null, ?string $tag = null, ?string $search = null): ActiveDataProvider
    {
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

    public function actionCountries(): array
    {
        return Store::countries();
    }

    public function actionTags(): array
    {
        return StoreTag::names();
    }
}
