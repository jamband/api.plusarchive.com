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

namespace app\tests;

use Yii;
use yii\base\InvalidConfigException;

trait RequestHelper
{
    public function request(string $url)
    {
        if (false === strpos($url, '?')) {
            $path = $url;
        } else {
            [$path, $query] = explode('?', $url);
            parse_str($query, $queries);
            Yii::$app->request->setQueryParams($queries);

            if (isset($queries['expand'])) {
                unset($queries['expand']);
            }
        }

        Yii::$app->request->setPathInfo($path);
        $request = Yii::$app->urlManager->parseRequest(Yii::$app->request);

        if (false === $request) {
            throw new InvalidConfigException('Unable to resolve the request: "'.$url.'"');
        }

        [$route, $params] = $request;

        if (isset($queries)) {
            $params = array_merge($queries, $params);
        }

        return Yii::$app->runAction($route, $params);
    }
}
