<?php

declare(strict_types=1);

namespace app\tests\console;

use app\commands\TapeController;
use app\models\Music;
use app\models\MusicGenre;
use app\tests\console\fixtures\TapeFixture;
use app\tests\Database;
use DateTime;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\helpers\FileHelper;
use yii\test\FixtureTrait;

/** @see TapeController */
class TapeControllerTest extends TestCase
{
    use FixtureTrait;

    private BufferedTapeController $controller;

    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable(Music::tableName());
        $this->db->createTable(MusicGenre::tableName());
        $this->db->createTable(MusicGenre::tableName().'_assn');

        $this->controller = new BufferedTapeController('tape', Yii::$app);
    }

    protected function tearDown(): void
    {
        FileHelper::removeDirectory(Yii::getAlias('@runtime/tape'));

        parent::tearDown();
    }

    public function fixtures(): array
    {
        return [
            'tape' => TapeFixture::class,
        ];
    }

    /** @see TapeController::actionFavorites() */
    public function testFavorites(): void
    {
        $this->getFixture('tape')->load();

        $this->assertSame(0, $this->controller->run('favorites', [1, 'Test Tape 1']));
        $this->assertSame('Created: '.Yii::getAlias('@runtime/tape')."/test-tape-1.json\n", $this->controller->flushStdOutBuffer());
        $this->assertFileExists(Yii::getAlias('@tape/test-tape-1.json'));

        $tape = file_get_contents(Yii::getAlias('@tape/test-tape-1.json'));
        $tape = json_decode($tape);

        $this->assertCount(5, get_object_vars($tape));
        $this->assertSame(1, $tape->id);
        $this->assertSame('Test Tape 1', $tape->title);
        $date = new DateTime;
        $this->assertSame('/'.$date->format('Y').'/'.$date->format('m').'/test-tape-1', $tape->path);
        $this->assertSame($date->format('M d, Y'), $tape->date);
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

class BufferedTapeController extends TapeController
{
    use StdOutBufferControllerTrait;
}
