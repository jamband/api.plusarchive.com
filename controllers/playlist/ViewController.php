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
use app\models\Playlist;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * @noinspection PhpUnused
 */
class ViewController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(string $id): Playlist
    {
        $model = Playlist::findOne(
            Yii::$app->hashids->decode($id)
        );

        if (null === $model) {
            throw new NotFoundHttpException('Not found.');
        }

        return $model;
    }
}
