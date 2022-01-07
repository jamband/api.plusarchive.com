<?php

declare(strict_types=1);

namespace app\tests\feature\track;

use app\controllers\track\SearchController;
use app\models\Music;
use app\models\MusicGenre;
use app\tests\Database;
use app\tests\feature\TestCase;
use yii\web\BadRequestHttpException;

/** @see SearchController */
class SearchControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(Music::tableName());
        $this->db->createTable(MusicGenre::tableName());
        $this->db->createTable(MusicGenre::tableName().'_assn');
    }

    public function testMissingParameters(): void
    {
        $this->expectExceptionObject(new BadRequestHttpException('Missing required parameters: q'));
        $this->endpoint('GET /track/search');
    }

    public function test(): void
    {
        $this->db->seeder('music', ['id'], [
            ['url1', Music::PROVIDER_BANDCAMP, 'key1', 'foo', 'image1', Music::TYPE_TRACK, false, time() + 2, time()],
            ['url2', Music::PROVIDER_BANDCAMP, 'key2', 'bar', 'image2', Music::TYPE_TRACK, false, time() + 1, time()],
            ['url3', Music::PROVIDER_BANDCAMP, 'key3', 'baz', 'image3', Music::TYPE_TRACK, false, time() + 3, time()],
        ]);

        $data = $this->endpoint('GET /tracks/search?expand=genres&q=o');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('foo', $data['items'][0]['title']);

        $data = $this->endpoint('GET /tracks/search?expand=genres&q=ba');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('bar', $data['items'][0]['title']);
        $this->assertSame('baz', $data['items'][1]['title']);
    }
}
