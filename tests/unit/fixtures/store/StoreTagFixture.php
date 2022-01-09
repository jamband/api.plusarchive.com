<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\store;

use app\tests\fixtures\BaseStoreTagFixture;

class StoreTagFixture extends BaseStoreTagFixture
{
    protected function getData(): array
    {
        return [
            'tag1' => [
                'name' => 'tag1',
                'frequency' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];
    }
}
