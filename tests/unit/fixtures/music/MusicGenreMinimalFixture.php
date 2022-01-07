<?php

declare(strict_types=1);

namespace app\tests\unit\fixtures\music;

use app\models\MusicGenre;
use yii\test\ActiveFixture;

class MusicGenreMinimalFixture extends ActiveFixture
{
    public $modelClass = MusicGenre::class;

    protected function getData(): array
    {
        return [
            'genre1' => [
                'name' => 'genre1',
                'frequency' => 3,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'genre2' => [
                'name' => 'genre2',
                'frequency' => 8,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'genre3' => [
                'name' => 'genre3',
                'frequency' => 2,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'genre4' => [
                'name' => 'genre4',
                'frequency' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ],
            'genre5' => [
                'name' => 'genre5',
                'frequency' => 5,
                'created_at' => time(),
                'updated_at' => time(),
            ],
        ];
    }
}
