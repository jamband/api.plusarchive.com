<?php

declare(strict_types=1);

namespace app\tests\feature\tracks;

use app\controllers\tracks\MinimalGenresController;
use app\models\MusicGenre;
use app\tests\Database;
use app\tests\feature\TestCase;

/** @see MinimalGenresController */
class MinimalGenresControllerTest extends TestCase
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
            ['genre1', 3, time(), time()],
            ['genre2', 10, time(), time()],
            ['genre3', 5, time(), time()],
            ['genre4', 9, time(), time()],
            ['genre5', 8, time(), time()],
        ]);

        $data = $this->endpoint('GET /tracks/minimal-genres?limit=3');
        $this->assertSame(200, $this->response->statusCode);
        $this->assertSame(['genre2', 'genre4', 'genre5'], $data);
    }
}
