<?php

declare(strict_types=1);

namespace app\tests\feature;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Yii;
use yii\web\Request;
use yii\web\Response;

/**
 * @property Request $request
 * @property Response $response
 */
class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->request = Yii::$app->getRequest();
        $this->response = Yii::$app->getResponse();

        unset($_SERVER['REQUEST_METHOD']);
    }

    public function endpoint(string $url): mixed
    {
        [$method, $url] = explode(' ', $url);
        $_SERVER['REQUEST_METHOD'] = $method;

        if (str_contains($url, '?')) {
            [$path, $query] = explode('?', $url);
            parse_str($query, $queries);
            $this->request->setQueryParams($queries);

            if (isset($queries['expand'])) {
                unset($queries['expand']);
            }
        } else {
            $path = $url;
        }

        $this->request->setPathInfo($path);
        $routeAndParams = Yii::$app->getUrlManager()->parseRequest($this->request);

        if (false === $routeAndParams) {
            return Yii::$app->runAction($path);
        }

        [$route, $params] = $routeAndParams;

        if (isset($queries)) {
            $params = array_merge($queries, $params);
        }

        return Yii::$app->runAction($route, $params);
    }
}
