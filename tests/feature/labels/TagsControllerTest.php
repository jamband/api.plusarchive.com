<?php

declare(strict_types=1);

namespace app\tests\feature\labels;

use app\controllers\labels\TagsController;
use app\models\Label;
use app\models\LabelTag;
use app\tests\Database;
use app\tests\feature\TestCase;

/** @see TagsController */
class TagsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->db = new Database;
        $this->db->createTable(Label::tableName());
        $this->db->createTable(LabelTag::tableName());
    }

    public function test(): void
    {
        $this->db->seeder('label', ['id'], [
            ['name1', 'foo', 'url1', 'link1', time(), time()],
            ['name2', 'bar', 'url2', 'link2', time(), time()],
            ['name3', 'baz', 'url3', 'link3', time(), time()],
            ['name4', 'foo', 'url4', 'link4', time(), time()],
        ]);

        $this->db->seeder('label_tag', ['id'], [
            ['tag1', 1, time(), time()],
            ['tag2', 1, time(), time()],
            ['tag3', 1, time(), time()],
        ]);

        $data = $this->endpoint('GET /labels/tags');
        $this->assertSame(['tag1', 'tag2', 'tag3'], $data);
    }
}
