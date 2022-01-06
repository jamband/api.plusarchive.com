<?php

declare(strict_types=1);

return [
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
];
