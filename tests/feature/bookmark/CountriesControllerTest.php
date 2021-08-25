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

namespace app\tests\feature\bookmark;

use app\tests\Database;
use app\tests\feature\TestCase;
use Yii;

class CountriesControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('bookmark');

        parent::setup();
    }

    public function test(): void
    {
        $this->db->seeder('bookmark', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
            ['name2', 'bar', 'url2', 'link2', time(), time()],
            ['name3', 'baz', 'url3', 'link3', time(), time()],
            ['name4', 'foo', 'url4', 'link4', time(), time()],
        ]);

        $data = $this->request('GET', '/bookmarks/countries');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(['bar', 'baz', 'foo'], $data);
    }
}
