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

namespace app\tests\console;

use app\models\Music;
use app\tests\Database;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\helpers\FileHelper;

class TapeControllerTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('music');
        $this->db->createTable('music_genre');
        $this->db->createTable('music_genre_assn');
    }

    protected function tearDown(): void
    {
        FileHelper::removeDirectory(Yii::getAlias('@runtime/tape'));
    }

    public function testFavorites(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'Foo1 Bar1', 'image1', Music::TYPE_TRACK, true, time(), time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'Foo2 Bar2', 'image2', Music::TYPE_TRACK, false, time(), time()],
            ['url3', Music::PROVIDER_BANDCAMP, 'key3', 'Foo3 Bar3', 'image3', Music::TYPE_TRACK, true, time(), time()],
        ]);

        $this->assertSame(0, Yii::$app->runAction('tape', [1, 'Test Tape 1']));
        $this->assertFileExists(Yii::getAlias('@tape/test-tape-1.json'));

        $tape = file_get_contents(Yii::getAlias('@tape/test-tape-1.json'));
        $tape = json_decode($tape);

        $this->assertCount(5, get_object_vars($tape));
        $this->assertSame(1, $tape->id);
        $this->assertSame('Test Tape 1', $tape->title);
        // 今日の日付になるので、テスト側もそれに対応する
//        $this->assertSame('/2021/08/test-tape-1', $tape->path);
//        $this->assertSame('Aug 21, 2021', $tape->date);
        $this->assertCount(2, $tape->items);

        $item1 = $tape->items[0];
        $this->assertCount(5, get_object_vars($item1));
        $this->assertSame('Foo1 Bar1', $item1->title);
        $this->assertSame('Bandcamp', $item1->provider);
        $this->assertSame('key1', $item1->provider_key);
        $this->assertSame('image1', $item1->image);
        $this->assertSame('foo1-bar1', $item1->slug);

        $item2 = $tape->items[1];
        $this->assertCount(5, get_object_vars($item2));
        $this->assertSame('Foo3 Bar3', $item2->title);
        $this->assertSame('Bandcamp', $item2->provider);
        $this->assertSame('key3', $item2->provider_key);
        $this->assertSame('image3', $item2->image);
        $this->assertSame('foo3-bar3', $item2->slug);
    }
}
