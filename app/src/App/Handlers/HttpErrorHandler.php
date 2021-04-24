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

use App\Exceptions\ContextAwareException;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

class HttpErrorHandler extends SlimErrorHandler
{
    protected function respond() : Response
    {
        $exception = $this->exception;

        $message = $exception->getMessage();
        $line = null;
        $context = [];
        $statusCode = 500;
        $trace = [];

        if ($exception instanceof ContextAwareException) {
            $statusCode = $exception->getCode();

            if ($this->displayErrorDetails) {
                $context = $exception->getContext();
            }
        }

        if ($this->displayErrorDetails) {
            $trace = $exception->getTrace();
            $line = $exception->getLine();
        }

        // Set up error response
        $response = $this->responseFactory->createResponse($statusCode);

        // Write out response
        $response->getBody()->write((string) json_encode([
            'error' => array_filter([
                'message' => $this->interpolate($message, array_merge([
                    'line' => $line,
                    'statusCode' => $statusCode,
                ], $context)),
                'line' => $line,
                'statusCode' => $statusCode,
                'context' => $context,
                'trace' => $trace,
            ])
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    protected function logError(string $error): void
    {
        $exception = $this->exception;

        $context = [];
        $logLevel = Logger::ERROR;

        if ($exception instanceof ContextAwareException) {
            $context = $exception->getContext();
            $logLevel = $exception->getLogLevel();
        }

        $this->logger->log($logLevel, $exception->getMessage(), $context);
    }

    protected function interpolate(string $message, array $context = []) : string
    {
        // Borrow the PSR-3 log message processor from composer to interpolate string context.
        return (new PsrLogMessageProcessor())([
            'message' => $message,
            'context' => $context,
        ])['message'];
    }
}
