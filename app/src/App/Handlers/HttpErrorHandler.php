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
use App\Exceptions\DivisionByZeroException;
use App\Exceptions\InternalServerErrorException;
use App\Exceptions\ParseException;
use App\Helpers\Responses;
use DivisionByZeroError;
use Error;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use ParseError;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

class HttpErrorHandler extends SlimErrorHandler
{
    protected function respond() : Response
    {
        $exception = $this->replaceException($this->exception);

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

        // Set status codes based on exception types
        $statusCode = match (get_class($exception)) {
            HttpBadRequestException::class => 400,
            HttpUnauthorizedException::class => 401,
            HttpForbiddenException::class => 403,
            HttpNotFoundException::class => 404,
            HttpMethodNotAllowedException::class => 405,
            HttpInternalServerErrorException::class => 500,
            HttpNotImplementedException::class => 501,
            default => $statusCode,
        };

        // Create a new response
        $response = $this->responseFactory->createResponse();

        return Responses::withError($response, $statusCode, array_filter([
            'message' => $this->interpolate($message, array_merge([
                'line' => $line,
                'statusCode' => $statusCode,
            ], $context)),
            'line' => $line,
            'statusCode' => $statusCode,
            'context' => $context,
            'trace' => $trace,
        ]));
    }

    protected function logError(string $error): void
    {
        $exception = $this->replaceException($this->exception);

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

    protected function replaceException(object $exception) : object
    {
        return match (get_class($exception)) {
            DivisionByZeroError::class => new DivisionByZeroException($exception->getMessage(), [], $exception),
            ParseError::class => new ParseException($exception->getMessage(), [], $exception),
            Error::class => new InternalServerErrorException($exception->getMessage(), [], $exception),
            default => $exception,
        };
    }
}
