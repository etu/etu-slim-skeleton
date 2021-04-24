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

use App\Controllers\ErrorController;
use App\Controllers\ExampleController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy as Group;

return function (App $app) {
    $app->get('/', ExampleController::class.':exampleAction');

    // These are routes that tries out different error scenarios that the error handler can manage.
    $app->get('/error/divisionByZero', ErrorController::class.':divisionByZeroAction');
    $app->get('/error/outOfMemory', ErrorController::class.':outOfMemoryAction');
    $app->get('/error/syntaxError', ErrorController::class.':syntaxErrorAction');
    $app->get('/error/undefinedFunction', ErrorController::class.':undefinedFunctionAction');
    $app->get('/error/undefinedClass', ErrorController::class.':undefinedClassAction');
};
