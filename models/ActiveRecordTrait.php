<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;

/**
 * @method static|ActiveQuery find()
 */
trait ActiveRecordTrait
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

    public static function hasName(string $name): bool
    {
        return in_array($name, static::names(), true);
    }
}
