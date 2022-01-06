<?php

declare(strict_types=1);

namespace app\filters;

use yii\filters\AccessControl as BaseAccessControl;
use yii\web\UnauthorizedHttpException;

class AccessControl extends BaseAccessControl
{
    public function denyAccess($user)
    {
        throw new UnauthorizedHttpException('Unauthenticated.');
    }
}
