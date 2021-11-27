<?php

declare(strict_types=1);

namespace app\queries;

use app\models\Label;
use creocoder\taggable\TaggableQueryBehavior;
use yii\db\ActiveQuery;

/**
 * @method LabelQuery allTagValues($values, $attribute = null)
 */
class LabelQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    public function init(): void
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

    public function country(string|null $country): ActiveQuery|LabelQuery
    {
        if (in_array($country, Label::countries(), true)) {
            return $this->andWhere(['country' => $country]);
        }

        return $this->nothing();
    }

    public function searchInNameOrder(string $search): LabelQuery
    {
        return $this->search($search)
            ->inNameOrder();
    }

    private function search(string $search): LabelQuery
    {
        return $this->andFilterWhere(['like', 'name', trim($search)]);
    }

    private function inNameOrder(): LabelQuery
    {
        return $this->orderBy(['name' => SORT_ASC]);
    }
}
