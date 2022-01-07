<?php

declare(strict_types=1);

namespace app\controllers\store;

use app\controllers\Controller;
use app\models\Store;

class CountriesController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(): array
    {
        return Store::countries();
    }
}
