<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\label;

use app\models\Label;
use yii\test\ActiveFixture;

class LabelFixture extends ActiveFixture
{
    public $modelClass = Label::class;

    protected function getData(): array
    {
        return [
            'label1' => [
                'name' => 'name1',
                'country' => 'country1',
                'url' => 'url1',
                'link' => 'link1',
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];
    }
}
