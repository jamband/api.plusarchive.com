<?php

declare(strict_types=1);

namespace app\controllers\tracks;

use app\models\Track;

class ViewController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(string $id): Track
    {
        return $this->findModel($id);
    }
}
