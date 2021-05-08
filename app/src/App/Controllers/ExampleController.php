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

use App\Models\Example;
use DateTime;
use Etu\Slim\Helpers\Responses;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExampleController
{
    public function exampleAction(Request $request, Response $response): Response
    {
        $model = new Example();
        $model->userAgent = $request->getHeader('User-Agent')[0] ?? 'N/A';
        $model->randomNumber = random_int(1, 6);
        $model->dateTime = new DateTime();

        return Responses::withData($response, $model);
    }
}
