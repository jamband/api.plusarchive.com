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

namespace app\queries;

use app\models\Music;
use yii\db\ActiveQuery;

class PlaylistQuery extends ActiveQuery
{
    use ActiveQueryTrait;

    public function init(): void
    {
        parent::init();

        $this->where(['type' => Music::TYPE_PLAYLIST]);
    }
}
