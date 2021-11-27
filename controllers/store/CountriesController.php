<?php

declare(strict_types=1);

namespace app\controllers\store;

use app\controllers\Controller;
use app\models\Store;

/**
 * @noinspection PhpUnused
 */
class CountriesController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): array
    {
        return Store::countries();
    }
}
