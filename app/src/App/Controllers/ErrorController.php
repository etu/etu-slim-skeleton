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

namespace App\Controllers;

use Etu\Slim\Helpers\Responses;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ErrorController
{
    public function divisionByZeroAction(Request $request, Response $response): Response
    {
        return Responses::withData($response, [
            'divisionByZero' => eval('47 / 0'),
        ]);
    }

    public function outOfMemoryAction(Request $request, Response $response): Response
    {
        return Responses::withData($response, [
            'outOfMemory' => str_repeat('a', PHP_INT_MAX),
        ]);
    }

    public function syntaxErrorAction(Request $request, Response $response): Response
    {
        return Responses::withData($response, [
            'syntaxError' => eval('::'),
        ]);
    }

    public function undefinedFunctionAction(Request $request, Response $response): Response
    {
        return Responses::withData($response, [
            'undefinedFunction' => eval('callToUndefinedFunction()'),
        ]);
    }

    public function undefinedClassAction(Request $request, Response $response): Response
    {
        return Responses::withData($response, [
            'undefinedClass' => eval('new UndefinedClass()'),
        ]);
    }
}
