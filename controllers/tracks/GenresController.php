<?php

declare(strict_types=1);

namespace app\controllers\tracks;

use app\controllers\Controller;
use app\models\MusicGenre;

class GenresController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(): array
    {
        return MusicGenre::names();
    }
}
