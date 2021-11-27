<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\controllers\Controller;
use app\models\MusicGenre;

/**
 * @noinspection PhpUnused
 */
class GenresController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(): array
    {
        return MusicGenre::names();
    }
}
