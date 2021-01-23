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

namespace app\tests\controllers\track;

use app\tests\Database;
use app\tests\WebTestCase;
use Yii;

class GenresControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        Database::createTable('music_genre');

        parent::setUp();
    }

    public function test(): void
    {
        Database::seeder('music_genre', ['id'], [
            ['genre1', 1, time(), time()],
            ['genre2', 1, time(), time()],
            ['genre3', 1, time(), time()],
        ]);

        $data = $this->request('GET', '/tracks/genres');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(['genre1', 'genre2', 'genre3'], $data);
    }
}
