<?php

declare(strict_types=1);

namespace app\tests;

use Yii;
use yii\di\Container;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Database|null $db = null;

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
