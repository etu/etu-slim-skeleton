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

use App\Handlers\HttpErrorHandler;
use App\Handlers\ShutdownHandler;
use App\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;

// Hide PHP error output
ini_set('display_errors', '0');

require_once(__DIR__ . '/../vendor/autoload.php');

// Set up PHP-DI
$containerBuilder = new ContainerBuilder();

// Enable PHP-DI cache on production
if (getenv('APP_ENV') !== 'local') {
    $containerBuilder->enableCompilation(__DIR__.'/../cache/');
}

// Load settings
(require_once(__DIR__.'/../src/bootstrap/settings.php'))($containerBuilder);

// Load dependencies
(require_once(__DIR__.'/../src/bootstrap/dependencies.php'))($containerBuilder);

// Build PHP-DI container instance
$container = $containerBuilder->build();

// Set up the app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register midlewares
(require_once(__DIR__.'/../src/bootstrap/middlewares.php'))($app);

// Register routes
(require_once(__DIR__.'/../src/bootstrap/routes.php'))($app);

/** @var SettingsInterface $settings */
$settings = $container->get(SettingsInterface::class);

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$errorHandler = new HttpErrorHandler(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    $container->get(LoggerInterface::class)
);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $settings->get('displayErrorDetails'));
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(
    $settings->get('displayErrorDetails'),
    $settings->get('logError'),
    $settings->get('logErrorDetails')
);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
