<?php

/*
 * This file is part of the api.plusarchive.com
 *
 * (c) Tomoki Morita <tmsongbooks215@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
