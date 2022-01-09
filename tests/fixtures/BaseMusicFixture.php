<?php

declare(strict_types=1);

namespace app\tests\fixtures;

use app\models\Music;
use yii\test\ActiveFixture;

class BaseMusicFixture extends ActiveFixture
{
    public $modelClass = Music::class;
}
