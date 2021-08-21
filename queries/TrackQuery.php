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

namespace app\queries;

use app\models\Music;
use creocoder\taggable\TaggableQueryBehavior;
use yii\db\ActiveQuery;

/**
 * @method TrackQuery allTagValues($values, $attribute = null)
 */
class TrackQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    public function init(): void
    {
        parent::init();

        $this->with(['genres'])
            ->where(['type' => Music::TYPE_TRACK]);
    }

    public function behaviors(): array
    {
        return [
            TaggableQueryBehavior::class,
        ];
    }

    public function provider(?string $provider): TrackQuery
    {
        $provider = array_search($provider, Music::PROVIDERS, true);

        if (false !== $provider) {
            return $this->andWhere(['provider' => $provider]);
        }

        return $this->nothing();
    }

    public function searchInTitleOrder(string $search): TrackQuery
    {
        return $this->search($search)
            ->inTitleOrder();
    }

    public function favoritesInLatestOrder(): TrackQuery
    {
        return $this->favorites()->latest();
    }

    private function search(string $search): TrackQuery
    {
        return $this->andFilterWhere(['like', 'title', trim($search)]);
    }

    private function inTitleOrder(): TrackQuery
    {
        return $this->orderBy(['title' => SORT_ASC]);
    }

    private function favorites(): TrackQuery
    {
        return $this->andWhere(['urge' => true]);
    }
}
