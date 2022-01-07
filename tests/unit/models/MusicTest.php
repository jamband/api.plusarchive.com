<?php

declare(strict_types=1);

namespace app\tests\unit\models;

use app\models\Music;
use app\tests\TestCase;

/** @see Music */
class MusicTest extends TestCase
{
    public function testTableName(): void
    {
        $this->assertSame('music', Music::tableName());
    }
}
