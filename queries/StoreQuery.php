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

use app\resources\Store;
use creocoder\taggable\TaggableQueryBehavior;
use yii\db\ActiveQuery;

/**
 * @method StoreQuery allTagValues($values, $attribute = null)
 */
class StoreQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    public function init()
    {
        parent::init();

        $this->with(['tags']);
    }

    public function behaviors(): array
    {
        return [
            TaggableQueryBehavior::class,
        ];
    }

    public function country(?string $country): StoreQuery
    {
        if (in_array($country, Store::countries(), true)) {
            return $this->andWhere(['country' => $country]);
        }

        return $this->andWhere(['country' => '']);
    }

    public function searchInNameOrder(string $search): StoreQuery
    {
        return $this->search($search)
            ->inNameOrder();
    }

    private function search(string $search): StoreQuery
    {
        return $this->andFilterWhere(['like', 'name', trim($search)]);
    }

    private function inNameOrder(): StoreQuery
    {
        return $this->orderBy(['name' => SORT_ASC]);
    }
}
