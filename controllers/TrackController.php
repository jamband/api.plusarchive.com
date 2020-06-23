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

use app\resources\MusicGenre;
use app\resources\Track;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class TrackController extends Controller
{
    public function actionIndex(?string $provider = null, ?string $genre = null, ?string $search = null): ActiveDataProvider
    {
        $query = Track::find();

        if (null !== $provider) {
            $query->provider($provider);
        }

        if (null !== $genre && '' !== $genre && MusicGenre::hasName($genre)) {
            $query->allTagValues($genre);
        }

        if (null !== $search) {
            $query->searchInTitleOrder($search);
        } else {
            $query->latest();
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 24,
            ],
        ]);
    }

    public function actionFavorites(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Track::find()->favoritesInLatestOrder(),
            'pagination' => false,
        ]);
    }

    public function actionMinimalGenres(int $limit): array
    {
        return MusicGenre::minimal($limit);
    }

    public function actionView(string $id): Track
    {
        $model = Track::findOne(Yii::$app->hashids->decode($id));

        if (null === $model) {
            throw new NotFoundHttpException('Page not found.');
        }

        return $model;
    }

    public function actionResources(): array
    {
        return [
            'genres' => MusicGenre::names(),
        ];
    }
}
