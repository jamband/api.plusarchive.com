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

namespace app\controllers\track;

use app\models\Track;
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
            'query' => Track::find()->searchInTitleOrder($q),
            'pagination' => [
                'pageSize' => self::PER_PAGE,
            ],
        ]);
    }
}
