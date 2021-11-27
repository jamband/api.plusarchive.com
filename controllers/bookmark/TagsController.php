<?php

declare(strict_types=1);

namespace app\controllers\bookmark;

use app\controllers\Controller;
use app\models\BookmarkTag;

/**
 * @noinspection PhpUnused
 */
class TagsController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): array
    {
        return BookmarkTag::names();
    }
}
