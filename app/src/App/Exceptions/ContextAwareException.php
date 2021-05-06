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

namespace App\Exceptions;

use Exception;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Throwable;

abstract class ContextAwareException extends Exception
{
    private int $logLevel;
    private array $context;

    public function __construct(
        string $message,
        int $code,
        int $logLevel = Logger::ERROR,
        array $context = [],
        ?Throwable $previous = null
    ) {
        $processed = (new PsrLogMessageProcessor())([
            'message' => $message,
            'context' => $context,
        ]);

        $this->logLevel = $logLevel;
        $this->context = $processed['context'];

        parent::__construct($processed['message'], $code, $previous);
    }

    public function getContext(): array
    {
        $context = [
            'php_line' => $this->context['errorLine'] ?? $this->getLine(),
            'php_file' => $this->context['errorFile'] ?? $this->getFile(),
            'php_exception' => get_class($this),
            'php_trace' => $this->getTraceAsString(),
        ];

        foreach ($this->context as $key => $value) {
            $context[$key] = $value;
        }

        return $context;
    }

    public function getLogLevel(): int
    {
        return $this->logLevel;
    }
}
