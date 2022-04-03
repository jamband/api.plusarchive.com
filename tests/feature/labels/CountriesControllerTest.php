<?php

declare(strict_types=1);

namespace app\tests\feature\labels;

use app\controllers\labels\CountriesController;
use app\models\Label;
use app\tests\Database;
use app\tests\feature\TestCase;

/** @see CountriesController */
class CountriesControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(Label::tableName());
    }

    public function test(): void
    {
        $this->db->seeder('label', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
            ['name2', 'bar', 'url2', 'link2', time(), time()],
            ['name3', 'baz', 'url3', 'link3', time(), time()],
            ['name4', 'foo', 'url4', 'link4', time(), time()],
        ]);

        $data = $this->endpoint('GET /labels/countries');
        $this->assertSame(200, $this->response->statusCode);
        $this->assertSame(['bar', 'baz', 'foo'], $data);
    }
}
