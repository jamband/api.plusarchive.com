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

namespace app\components;

use Hashids\Hashids as HashidsBase;
use yii\base\BaseObject;

class Hashids extends BaseObject
{
    public $salt;
    public $minHashLength;
    public $alphabet;

    /**
     * @var HashidsBase
     */
    private $_hashids;

    public function init(): void
    {
        parent::init();

        $this->_hashids = new HashidsBase(
            $this->salt,
            $this->minHashLength,
            $this->alphabet
        );
    }

    public function __call($name, $params)
    {
        if (method_exists($this->_hashids, $name)) {
            return call_user_func_array([$this->_hashids, $name], $params);
        }

        return parent::__call($name, $params);
    }

    public function decode(string $id): int
    {
        $id = $this->_hashids->decode($id);

        if (1 === count($id)) {
            return $id[0];
        }

        return 0;
    }
}
