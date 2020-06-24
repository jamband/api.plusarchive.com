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

use app\resources\Label;
use app\resources\LabelTag;
use yii\data\ActiveDataProvider;

class LabelController extends Controller
{
    public function actionIndex(?string $country = null, ?string $tag = null, ?string $search = null): ActiveDataProvider
    {
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

    public function actionResources(): array
    {
        return [
            'countries' => Label::countries(),
            'tags' => LabelTag::names(),
        ];
    }
}
