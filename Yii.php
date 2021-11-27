<?php

declare(strict_types=1);

class Yii extends yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication
     */
    public static $app;
}

/**
 * @property app\components\Hashids $hashids
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 *
 */
class WebApplication extends yii\web\Application
{
}

/**
 *
 */
class ConsoleApplication extends yii\console\Application
{
}
