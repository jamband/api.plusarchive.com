<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function __construct(
        Container $container,
        private ResponseFactory $response,
    ) {
        parent::__construct($container);
    }

    public function render($request, Throwable $e): Response
    {
        if ($e instanceof BadRequestHttpException) {
            return $this->response->json(
                data: ['message' => $e->getMessage()],
                status: $e->getStatusCode(),
            );
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->response->json(
                data: ['message' => 'Not Found.'],
                status: $e->getStatusCode(),
            );
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->response->json(
                data: ['message' => 'Model Not Found.'],
                status: 404,
            );
        }

        if ($e instanceof ValidationException) {
            $errors = [];
            foreach ($e->errors() as $attribute => $message) {
                $errors[$attribute] = $message[0];
            }

            return $this->response->json(
                data: ['errors' => $errors],
                status: $e->status,
            );
        }

        return parent::render($request, $e);
    }
}
