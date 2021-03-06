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

use app\models\MusicGenre;
use app\models\Track;
use yii\data\ActiveDataProvider;

/**
 * @noinspection PhpUnused
 */
class IndexController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(?string $provider = null, ?string $genre = null): ActiveDataProvider
    {
        $query = Track::find();

        if (null !== $provider) {
            $query->provider($provider);
        }

        if (null !== $genre) {
            if (MusicGenre::hasName($genre)) {
                $query->allTagValues($genre);
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
