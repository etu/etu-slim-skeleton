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

use DI\ContainerBuilder;
use Etu\Slim\Handlers\HttpErrorHandler;
use Etu\Slim\Handlers\ShutdownHandler;
use Etu\Slim\Settings\SettingsInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;

// Hide PHP error output
ini_set('display_errors', '0');

// Start output buffering to be able to control the output in the shutdown handler
ob_start();

require_once(__DIR__ . '/../vendor/autoload.php');

// Set up PHP-DI
$containerBuilder = new ContainerBuilder();

// Enable PHP-DI cache on production
if (getenv('APP_ENV') !== 'local') {
    $containerBuilder->enableCompilation(__DIR__ . '/../cache/');
}

// Load settings
(require_once(__DIR__ . '/../src/bootstrap/settings.php'))($containerBuilder);

// Load dependencies
(require_once(__DIR__ . '/../src/bootstrap/dependencies.php'))($containerBuilder);

// Build PHP-DI container instance
$container = $containerBuilder->build();

// Set up the app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register midlewares
(require_once(__DIR__ . '/../src/bootstrap/middlewares.php'))($app);

// Register routes
(require_once(__DIR__ . '/../src/bootstrap/routes.php'))($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

// Create Request object from globals
$request = ServerRequestCreatorFactory::create()->createServerRequestFromGlobals();

// Create Error Handler
$errorHandler = new HttpErrorHandler(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    $container->get(LoggerInterface::class)
);

// Register Shutdown Handler
register_shutdown_function(new ShutdownHandler($request, $errorHandler, $settings->get('displayErrorDetails')));

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$app->addErrorMiddleware(
    $settings->get('displayErrorDetails'),
    $settings->get('logError'),
    $settings->get('logErrorDetails')
)->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
(new ResponseEmitter())->emit($app->handle($request));
