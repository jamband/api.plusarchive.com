<?php

declare(strict_types=1);

namespace app\tests\feature\tracks;

use app\controllers\tracks\GenresController;
use app\models\MusicGenre;
use app\tests\Database;
use app\tests\feature\TestCase;

/** @see GenresController */
class GenresControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(MusicGenre::tableName());
    }

    public function test(): void
    {
        $this->db->seeder('music_genre', ['id'], [
            ['genre1', 1, time(), time()],
            ['genre2', 1, time(), time()],
            ['genre3', 1, time(), time()],
        ]);

        $data = $this->endpoint('GET /tracks/genres');
        $this->assertSame(200, $this->response->statusCode);
        $this->assertSame(['genre1', 'genre2', 'genre3'], $data);
    }
}
