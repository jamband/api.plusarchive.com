<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\models\Track;

/**
 * @noinspection PhpUnused
 */
class ViewController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(string $id): Track
    {
        return $this->findModel($id);
    }
}
