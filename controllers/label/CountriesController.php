<?php

declare(strict_types=1);

namespace app\controllers\label;

use app\controllers\Controller;
use app\models\Label;

/**
 * @noinspection PhpUnused
 */
class CountriesController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): array
    {
        return Label::countries();
    }
}
