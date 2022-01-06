<?php

declare(strict_types=1);

namespace app\tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Yii;
use yii\di\Container;
use yii\test\FixtureTrait;

class TestCase extends BaseTestCase
{
    use FixtureTrait;

    protected function tearDown(): void
    {
        Yii::$container = new Container;
    }
}
