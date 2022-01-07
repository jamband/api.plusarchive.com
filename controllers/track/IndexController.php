<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\models\MusicGenre;
use app\models\Track;
use yii\data\ActiveDataProvider;

class IndexController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(
        string|null $provider = null,
        string|null $genre = null,
    ): ActiveDataProvider {
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
