<?php
/**
 * Etu's Slim Framework 4 Skeleton Application.
 *
 * MIT License
 *
 * Copyright (c) 2021 Elis Hirwing <elis@hirwing.se>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 */
declare(strict_types=1);

namespace App\Handlers;

use App\Exceptions\InternalServerErrorException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\ResponseEmitter;

class ShutdownHandler
{
    private Request $request;
    private HttpErrorHandler $errorHandler;
    private bool $displayErrorDetails;

    public function __construct(
        Request $request,
        HttpErrorHandler $errorHandler,
        bool $displayErrorDetails
    ) {
        $this->request = $request;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
    }

    public function __invoke()
    {
        $error = error_get_last();

        if ($error) {
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                $message = match ($error['type']) {
                    E_USER_ERROR => 'FATAL ERROR: {errorMessage}. on line {errorLine} in file {errorFile}.',
                    E_USER_WARNING => 'WARNING: {errorMessage}. on line {errorLine} in file {errorFile}.',
                    E_USER_NOTICE => 'NOTICE: {errorMessage}. on line {errorLine} in file {errorFile}.',
                    default => 'ERROR: {errorMessage}. on line {errorLine} in file {errorFile}.',
                };
            }

            $exception = new InternalServerErrorException($message, [
                'errorMessage' => $error['message'],
                'errorLine' => $error['line'],
                'errorFile' => $error['file'],
                'errorType' => $error['type'],
            ]);

            $response = $this->errorHandler->__invoke(
                $this->request,
                $exception,
                $this->displayErrorDetails,
                true,
                true
            );

            $responseEmitter = new ResponseEmitter();
            $responseEmitter->emit($response);
        }
    }
}
