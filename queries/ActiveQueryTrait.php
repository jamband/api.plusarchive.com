<?php

declare(strict_types=1);

namespace app\queries;

use yii\db\ActiveQuery;

trait ActiveQueryTrait
{
    public function latest(string $column = 'created_at'): ActiveQuery
    {
        return $this->orderBy([$column => SORT_DESC]);
    }

    public function nothing(): ActiveQuery
    {
        return $this->where('1 = 0');
    }
}
