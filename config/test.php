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

return yii\helpers\ArrayHelper::merge(require __DIR__.'/web.php', [
    'id' => 'test',
    'components' => [
        'db' => null,
        'request' => [
            'hostInfo' => 'https://api.example.com',
            'scriptUrl' => '/index.php',
        ],
        'hashids' => [
            'salt' => 'test',
        ],
    ],
]);
