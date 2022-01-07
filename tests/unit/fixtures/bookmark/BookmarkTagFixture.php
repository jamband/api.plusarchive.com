<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\bookmark;

use app\models\BookmarkTag;
use yii\test\ActiveFixture;

class BookmarkTagFixture extends ActiveFixture
{
    public $modelClass = BookmarkTag::class;

    public $depends = [
    ];

    protected function getData(): array
    {
        return [
            'tag1' => [
                'name' => 'name1',
                'frequency' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];
    }
}
