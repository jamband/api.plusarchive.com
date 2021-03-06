<?php

declare(strict_types=1);

namespace app\tests;

use Yii;
use yii\web\Response;

class WebTestCase extends TestCase
{
    protected function setUp(): void
    {
        Yii::$app->set('response', new Response);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_METHOD']);

        parent::tearDown();
    }

    public function request(string $method, string $url, array $params = [])
    {
        $_SERVER['REQUEST_METHOD'] = $method;

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

        if (!empty($params)) {
            Yii::$app->request->setBodyParams($params);
        }

        Yii::$app->request->setPathInfo($path);
        $routeAndParams = Yii::$app->urlManager->parseRequest(Yii::$app->request);

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
