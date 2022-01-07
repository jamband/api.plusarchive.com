<?php

declare(strict_types=1);

namespace app\tests\feature\label;

use app\controllers\label\SearchController;
use app\models\Label;
use app\models\LabelTag;
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
        $this->db->createTable(Label::tableName());
        $this->db->createTable(LabelTag::tableName());
        $this->db->createTable(LabelTag::tableName().'_assn');
    }

    public function testBadRequest(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->endpoint('GET /labels/search');
    }

    public function test(): void
    {
        $this->db->seeder('label', ['id'], [
            ['foo', 'country1', 'url1', 'link1', time() + 2, time()],
            ['bar', 'country2', 'url2', 'link2', time() + 1, time()],
            ['baz', 'country3', 'url3', 'link3', time() + 3, time()],
        ]);

        $data = $this->endpoint('GET /labels/search?expand=tags&q=o');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(1, $data['_meta']['totalCount']);
        $this->assertSame('foo', $data['items'][0]['name']);

        $data = $this->endpoint('GET /labels/search?expand=tags&q=ba');
        $this->assertSame(200, $this->response->statusCode);

        $this->assertSame(2, $data['_meta']['totalCount']);
        $this->assertSame('bar', $data['items'][0]['name']);
        $this->assertSame('baz', $data['items'][1]['name']);
    }
}
