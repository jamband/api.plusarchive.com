<?php

declare(strict_types=1);

namespace app\tests\feature\tracks;

use app\controllers\tracks\ViewController;
use app\models\Music;
use app\models\MusicGenre;
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
        $this->db->createTable(MusicGenre::tableName());
        $this->db->createTable(MusicGenre::tableName().'_assn');
    }

    public function testNotFound(): void
    {
        $this->expectExceptionObject(new NotFoundHttpException('Not Found.'));
        $this->endpoint('GET /tracks/'.Yii::$app->hashids->encode(1));
    }

    public function test(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'title1', 'image1', Music::TYPE_TRACK, false, time(), time()],
        ]);

        $data = $this->endpoint('GET /tracks/'.Yii::$app->hashids->encode(1));
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame('url1', $data['url']);
    }
}
