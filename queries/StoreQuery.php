<?php

declare(strict_types=1);

namespace app\queries;

use app\models\Store;
use creocoder\taggable\TaggableQueryBehavior;
use yii\db\ActiveQuery;

/**
 * @method ActiveQuery allTagValues($values, $attribute = null)
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

    public function country(string|null $country): ActiveQuery|StoreQuery
    {
        if (in_array($country, Store::countries(), true)) {
            return $this->andWhere(['country' => $country]);
        }

        return $this->nothing();
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
