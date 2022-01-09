<?php

declare(strict_types=1);

namespace app\tests\fixtures;

use app\models\BookmarkTag;
use yii\test\ActiveFixture;

class BaseBookmarkTagFixture extends ActiveFixture
{
    public $modelClass = BookmarkTag::class;
}
