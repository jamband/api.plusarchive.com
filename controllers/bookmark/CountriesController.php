<?php

declare(strict_types=1);

namespace app\controllers\bookmark;

use app\controllers\Controller;
use app\models\Bookmark;

/**
 * @noinspection PhpUnused
 */
class CountriesController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): array
    {
        return Bookmark::countries();
    }
}
