<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\bookmark;

use app\tests\fixtures\BaseBookmarkFixture;

class BookmarkFieldsFixture extends BaseBookmarkFixture
{
    protected function getData(): array
    {
        return [
            'bookmark1' => [
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
