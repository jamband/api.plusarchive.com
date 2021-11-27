<?php

declare(strict_types=1);

namespace app\tests\feature\track;

use app\tests\Database;
use app\tests\feature\TestCase;
use Yii;

class GenresControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->db = new Database;
        $this->db->createTable('music_genre');

        parent::setUp();
    }

    public function test(): void
    {
        $this->db->seeder('music_genre', ['id'], [
            ['genre1', 1, time(), time()],
            ['genre2', 1, time(), time()],
            ['genre3', 1, time(), time()],
        ]);

        $data = $this->request('GET', '/tracks/genres');
        $this->assertSame(200, Yii::$app->response->statusCode);
        $this->assertSame(['genre1', 'genre2', 'genre3'], $data);
    }
}
