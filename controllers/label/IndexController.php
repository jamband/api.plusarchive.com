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

namespace app\controllers\label;

use app\models\Label;
use app\models\LabelTag;
use yii\data\ActiveDataProvider;

/**
 * @noinspection PhpUnused
 */
class IndexController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(?string $country = null, ?string $tag = null): ActiveDataProvider
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

        return new ActiveDataProvider([
            'query' => $query->latest(),
            'pagination' => [
                'pageSize' => self::PER_PAGE,
            ],
        ]);
    }
}
