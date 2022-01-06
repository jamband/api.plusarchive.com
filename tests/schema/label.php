<?php

declare(strict_types=1);

return [
    'id' => 'INTEGER PRIMARY KEY',
    'name' => 'TEXT NOT NULL',
    'country' => 'TEXT NOT NULL',
    'url' => 'TEXT NOT NULL',
    'link' => 'TEXT NOT NULL',
    'created_at' => 'INTEGER NOT NULL',
    'updated_at' => 'INTEGER NOT NULL',
];