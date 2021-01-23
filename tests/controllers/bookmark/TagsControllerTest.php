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

namespace app\tests\controllers\bookmark;

use app\tests\Database;
use app\tests\WebTestCase;
use Yii;

class TagsControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::createTable('bookmark');
        Database::createTable('bookmark_tag');

        parent::setUp();
    }

    public function test(): void
    {
        Database::seeder('bookmark', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
            ['name2', 'bar', 'url2', 'link2', time(), time()],
            ['name3', 'baz', 'url3', 'link3', time(), time()],
            ['name4', 'foo', 'url4', 'link4', time(), time()],
        ]);

        Database::seeder('bookmark_tag', ['id'], [
            ['tag1', 1, time(), time()],
            ['tag2', 1, time(), time()],
            ['tag3', 1, time(), time()],
        ]);

        $data = $this->request('GET', '/bookmarks/tags');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(['tag1', 'tag2', 'tag3'], $data);
    }
}
