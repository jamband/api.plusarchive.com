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

namespace app\tests\components;

use PHPUnit\Framework\TestCase;
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
