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

namespace app\tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Yii;
use yii\db\Connection;

class TestCase extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        Yii::$app->set('db', [
            'class' => Connection::class,
            'dsn' => 'sqlite::memory:',
        ]);
    }

    protected function tearDown(): void
    {
        Yii::$app->db->close();
    }
}
