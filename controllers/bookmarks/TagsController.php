<?php

declare(strict_types=1);

namespace app\controllers\bookmarks;

use app\controllers\Controller;
use app\models\BookmarkTag;

class TagsController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(): array
    {
        return BookmarkTag::names();
    }
}
