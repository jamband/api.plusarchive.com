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

use app\resources\Label;
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

    public function country(?string $country): LabelQuery
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
