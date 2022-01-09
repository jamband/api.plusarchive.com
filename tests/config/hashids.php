<?php

declare(strict_types=1);

use app\components\Hashids;

return [
    'class' => Hashids::class,
    'salt' => 'test',
    'minHashLength' => 11,
    'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-',
];
