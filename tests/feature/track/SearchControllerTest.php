<?php

declare(strict_types=1);

namespace app\tests\feature\track;

use app\models\Music;
use app\tests\Database;
use app\tests\feature\TestCase;
use Yii;
use yii\web\BadRequestHttpException;

class SearchControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('music');
        $this->db->createTable('music_genre');
        $this->db->createTable('music_genre_assn');

        parent::setUp();
    }

    public function testBadRequest(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->request('GET', '/track/search');
    }

    public function test(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'foo', 'image1', Music::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'bar', 'image2', Music::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Music::PROVIDER_BANDCAMP, 'key3', 'baz', 'image3', Music::TYPE_TRACK, false, time() + 3, time()],
        ]);

        $data = $this->request('GET', '/tracks/search?expand=genres&q=o');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('foo', $data['items'][0]['title']);

        $data = $this->request('GET', '/tracks/search?expand=genres&q=ba');
        $this->assertSame(200, Yii::$app->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('bar', $data['items'][0]['title']);
        $this->assertSame('baz', $data['items'][1]['title']);
    }
}