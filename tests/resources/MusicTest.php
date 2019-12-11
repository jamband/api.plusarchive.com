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

namespace app\tests\resources;

use app\resources\Music;
use app\tests\TestCase;

class MusicTest extends TestCase
{
    public function testTableName(): void
    {
        $this->assertSame('music', Music::tableName());
    }
}
