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

namespace app\controllers\playlist;

use app\controllers\Controller;
use yii\data\ActiveDataProvider;
use app\resources\Playlist;

/**
 * @noinspection PhpUnused
 */
class IndexController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Playlist::find()->latest(),
            'pagination' => false,
        ]);
    }
}
