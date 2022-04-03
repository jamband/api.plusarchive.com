<?php

declare(strict_types=1);

namespace app\controllers\bookmarks;

use app\controllers\Controller;
use app\models\Bookmark;

class CountriesController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(): array
    {
        return Bookmark::countries();
    }
}
