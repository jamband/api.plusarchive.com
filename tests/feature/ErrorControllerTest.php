<?php

declare(strict_types=1);

namespace app\tests\feature;

use app\controllers\ErrorController;
use yii\base\InvalidRouteException;

/** @see ErrorController */
class ErrorControllerTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->expectException(InvalidRouteException::class);
        $this->endpoint('GET /');
    }
}
