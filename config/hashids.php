<?php

declare(strict_types=1);

return [
    'salt' => env('HASHIDS_SALT'),
    'minHashLength' => 11,
    'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-',
];
