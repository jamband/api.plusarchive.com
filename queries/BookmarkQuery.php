<?php

declare(strict_types=1);

namespace app\queries;

use app\models\Bookmark;
use creocoder\taggable\TaggableQueryBehavior;
use yii\db\ActiveQuery;

/**
 * @method BookmarkQuery allTagValues($values, $attribute = null)
 */
class BookmarkQuery extends ActiveQuery
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

    public function country(string|null $country): ActiveQuery|BookmarkQuery
    {
        if (in_array($country, Bookmark::countries(), true)) {
            return $this->andWhere(['country' => $country]);
        }

        return $this->nothing();
    }

    public function searchInNameOrder(string $search): BookmarkQuery
    {
        return $this->search($search)
            ->inNameOrder();
    }

    private function search(string $search): BookmarkQuery
    {
        return $this->andFilterWhere(['like', 'name', trim($search)]);
    }

    private function inNameOrder(): BookmarkQuery
    {
        return $this->orderBy(['name' => SORT_ASC]);
    }
}
