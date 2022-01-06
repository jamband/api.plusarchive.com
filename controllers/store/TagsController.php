<?php

declare(strict_types=1);

namespace app\controllers\store;

use app\controllers\Controller;
use app\models\StoreTag;

class TagsController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): array
    {
        return StoreTag::names();
    }
}
