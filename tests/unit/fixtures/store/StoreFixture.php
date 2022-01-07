<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\store;

use app\models\Store;
use yii\test\ActiveFixture;

class StoreFixture extends ActiveFixture
{
    public $modelClass = Store::class;

    protected function getData(): array
    {
        return [
            'store1' => [
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
