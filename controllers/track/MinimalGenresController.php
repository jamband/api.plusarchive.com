<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\controllers\Controller;
use app\models\MusicGenre;

/**
 * @noinspection PhpUnused
 */
class MinimalGenresController extends Controller
{
    protected array $verbs = ['GET'];

    public function actionIndex(int $limit): array
    {
        return MusicGenre::minimal($limit);
    }
}
