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

use Yii;
use yii\di\Container;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var Database|null */
    protected $db;

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
        if (null !== $this->db) {
            Yii::$app->db->close();
        }

        Yii::$container = new Container;
    }
}
