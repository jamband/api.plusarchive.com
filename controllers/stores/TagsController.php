<?php

declare(strict_types=1);

namespace app\controllers\stores;

use app\controllers\Controller;
use app\models\StoreTag;

class TagsController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(): array
    {
        return StoreTag::names();
    }
}
