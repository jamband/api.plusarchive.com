<?php

declare(strict_types=1);

namespace app\tests;

use Yii;
use yii\db\Connection;

class Database
{
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

    public function createTable(string $table, array $columns = []): void
    {
        Yii::$app->getDb()->createCommand()
            ->createTable($table, empty($columns)
                ? require sprintf(__DIR__.'/schema/%s.php', $table)
                : $columns
            )->execute();
    }
}
