<?php

declare(strict_types=1);

namespace app\tests;

use Yii;
use yii\db\Connection;

class Database
{
    public const TABLE_BOOKMARK = 'bookmark';
    public const TABLE_BOOKMARK_TAG = 'bookmark_tag';
    public const TABLE_BOOKMARK_TAG_ASSN = 'bookmark_tag_assn';
    public const TABLE_LABEL = 'label';
    public const TABLE_LABEL_TAG = 'label_tag';
    public const TABLE_LABEL_TAG_ASSN = 'label_tag_assn';
    public const TABLE_MUSIC = 'music';
    public const TABLE_MUSIC_GENRE = 'music_genre';
    public const TABLE_MUSIC_GENRE_ASSN = 'music_genre_assn';
    public const TABLE_STORE = 'store';
    public const TABLE_STORE_TAG = 'store_tag';
    public const TABLE_STORE_TAG_ASSN = 'store_tag_assn';

    public function __construct()
    {
        Yii::$app->set('db', [
            'class' => Connection::class,
            'dsn' => 'sqlite::memory:',
            'charset' => 'utf8',
        ]);
    }

    public function seeder(string $table, array $exceptColumns, array $rows): void
    {
        $columns = Yii::$app->getDb()->getTableSchema($table)->getColumnNames();
        $columns = array_values(array_diff($columns, $exceptColumns));

        Yii::$app->getDb()->createCommand()
            ->batchInsert($table, $columns, $rows)
            ->execute();
    }

    public function createTable(string $table): void
    {
        Yii::$app->getDb()->createCommand()
            ->createTable($table, require sprintf(__DIR__.'/schema/%s.php', $table))
            ->execute();
    }
}
