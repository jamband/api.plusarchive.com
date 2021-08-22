<?php

/*
 * This file is part of the api.plusarchive.com
 *
 * (c) Tomoki Morita <tmsongbooks215@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace app\tests;

use Yii;
use yii\db\Connection;

class Database
{
    protected const SCHEMA = [
        'bookmark' => [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'country' => 'TEXT NOT NULL',
            'url' => 'TEXT NOT NULL',
            'link' => 'TEXT NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ],
        'bookmark_tag' => [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'frequency' => 'INTEGER NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ],
        'bookmark_tag_assn' => [
            'bookmark_id' => 'INTEGER',
            'bookmark_tag_id' => 'INTEGER',
            'PRIMARY KEY (`bookmark_id`, `bookmark_tag_id`)',
        ],
        'label' => [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'country' => 'TEXT NOT NULL',
            'url' => 'TEXT NOT NULL',
            'link' => 'TEXT NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ],
        'label_tag' => [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'frequency' => 'INTEGER NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ],
        'label_tag_assn' => [
            'label_id' => 'INTEGER',
            'label_tag_id' => 'INTEGER',
            'PRIMARY KEY (`label_id`, `label_tag_id`)',
        ],
        'music' => [
            'id' => 'INTEGER PRIMARY KEY',
            'url' => 'TEXT NOT NULL',
            'provider' => 'INTEGER NOT NULL',
            'provider_key' => 'TEXT NOT NULL',
            'title' => 'TEXT NOT NULL',
            'image' => 'TEXT NOT NULL',
            'type' => 'INTEGER NOT NULL',
            'urge' => 'INTEGER NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ],
        'music_genre' => [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'frequency' => 'INTEGER NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ],
        'music_genre_assn' => [
            'music_id' => 'INTEGER',
            'music_genre_id' => 'INTEGER',
            'PRIMARY KEY (`music_id`, `music_genre_id`)',
        ],
        'store' => [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'country' => 'TEXT NOT NULL',
            'url' => 'TEXT NOT NULL',
            'link' => 'TEXT NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ],
        'store_tag' => [
            'id' => 'INTEGER PRIMARY KEY',
            'name' => 'TEXT NOT NULL',
            'frequency' => 'INTEGER NOT NULL',
            'created_at' => 'INTEGER NOT NULL',
            'updated_at' => 'INTEGER NOT NULL',
        ],
        'store_tag_assn' => [
            'store_id' => 'INTEGER',
            'store_tag_id' => 'INTEGER',
            'PRIMARY KEY (`store_id`, `store_tag_id`)',
        ],
    ];

    private Connection $db;

    public function __construct()
    {
        Yii::$app->set('db', [
            'class' => Connection::class,
            'dsn' => 'sqlite::memory:',
        ]);

        $this->db = Yii::$app->db;
    }

    public function seeder(string $table, array $exceptColumns, array $rows): void
    {
        $columns = $this->db->getTableSchema($table)->getColumnNames();
        $columns = array_values(array_diff($columns, $exceptColumns));

        $this->db->createCommand()
            ->batchInsert($table, $columns, $rows)
            ->execute();
    }

    public function createTable(string $table): void
    {
        $this->db->createCommand()
            ->createTable($table, static::SCHEMA[$table])
            ->execute();
    }
}
