<?php

declare(strict_types=1);

namespace app\controllers\label;

use app\controllers\Controller;
use app\models\LabelTag;

/**
 * @noinspection PhpUnused
 */
class TagsController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): array
    {
        return LabelTag::names();
    }
}
