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
