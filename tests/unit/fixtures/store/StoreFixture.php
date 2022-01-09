<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\store;

use app\tests\fixtures\BaseStoreFixture;

class StoreFixture extends BaseStoreFixture
{
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
