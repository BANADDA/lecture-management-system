<?php

use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
            return response()->view('errors.unauthorized', [], 403);
        }

        return parent::render($request, $exception);
    }
}
