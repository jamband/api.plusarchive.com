<?php

declare(strict_types=1);

namespace app\controllers\track;

use app\controllers\Controller;
use app\models\MusicGenre;

class MinimalGenresController extends Controller
{
    protected string $role = '';
    protected string $verb = 'GET';

    public function actionIndex(int $limit): array
    {
        return MusicGenre::minimal($limit);
    }
}
