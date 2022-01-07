<?php

declare(strict_types=1);

namespace app\tests\feature\playlist;

use app\controllers\playlist\ViewController;
use app\models\Music;
use app\tests\Database;
use app\tests\feature\TestCase;
use Yii;
use yii\web\NotFoundHttpException;

/** @see ViewController */
class ViewControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(Music::tableName());
    }

    public function testNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->endpoint('GET /playlists/'.Yii::$app->hashids->encode(1));
    }

    public function test(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_SOUNDCLOUD, 'key1', 'title1', 'image1', Music::TYPE_PLAYLIST, false, time(), time()],
        ]);

        $data = $this->endpoint('GET /playlists/'.Yii::$app->hashids->encode(1));
        $this->assertSame('url1', $data['url']);
    }
}
