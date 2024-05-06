<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return function (Exceptions $exceptions) {
    $exceptions->render(function (Throwable $e) {
        /** @var ResponseFactory $response */
        $response = Application::getInstance()->make(ResponseFactory::class);

        if ($e instanceof BadRequestHttpException) {
            return $response->make(
                ['message' => $e->getMessage()],
                $e->getStatusCode(),
            );
        }

        if ($e instanceof NotFoundHttpException) {
            return $response->make(
                ['message' => 'Not Found.'],
                $e->getStatusCode(),
            );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $response->make(
                ['message' => 'Method Not Allowed.'],
                $e->getStatusCode(),
            );
        }

        if ($e instanceof ValidationException) {
            $errors = [];
            foreach ($e->errors() as $attribute => $message) {
                $errors[$attribute] = $message[0];
            }

            return $response->make(
                ['errors' => $errors],
                $e->status,
            );
        }

        return null;
    });
};
