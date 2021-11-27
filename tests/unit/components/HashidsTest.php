<?php

declare(strict_types=1);

namespace app\tests\unit\components;

use app\tests\TestCase;
use Yii;

class HashidsTest extends TestCase
{
    public function testSalt(): void
    {
        $this->assertSame('test', Yii::$app->hashids->salt);
    }

    public function testEncode(): void
    {
        $this->assertMatchesRegularExpression('/\A[\w-]{11}\z/', Yii::$app->hashids->encode(1));
    }

    public function testDecode(): void
    {
        $id = Yii::$app->hashids->encode(1);
        $this->assertSame(1, Yii::$app->hashids->decode($id));
    }
}
