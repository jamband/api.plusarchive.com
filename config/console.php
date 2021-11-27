<?php

declare(strict_types=1);

return yii\helpers\ArrayHelper::merge(require __DIR__.'/base.php', [
    'id' => 'console',
    'controllerNamespace' => 'app\commands',
]);
