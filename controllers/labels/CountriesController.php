<?php

declare(strict_types=1);

namespace app\controllers\labels;

use app\controllers\Controller;
use app\models\Label;

class CountriesController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(): array
    {
        return Label::countries();
    }
}
