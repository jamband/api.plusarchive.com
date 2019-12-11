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

namespace app\resources;

use yii\db\ActiveQuery;

/**
 * @method static|ActiveQuery find()
 */
trait ResourceTrait
{
    public static function names(): array
    {
        return static::find()
            ->select('name')
            ->orderBy(['name' => SORT_ASC])
            ->column();
    }

    public static function countries(): array
    {
        return static::find()
            ->select('country')
            ->distinct()
            ->orderBy(['country' => SORT_ASC])
            ->column();
    }
}
