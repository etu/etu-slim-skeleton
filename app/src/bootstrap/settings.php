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
use Etu\Slim\Settings\Settings;
use Etu\Slim\Settings\SettingsInterface;
use Psr\Log\LogLevel;

return function (ContainerBuilder $containerBuilder) {
    // Global settings object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => (getenv('APP_DEBUG') === 'true'),
                'logError' => true,
                'logErrorDetails' => true,
                'logger' => [
                    'name' => getenv('APP_NAME'),
                    'path' => 'php://stdout',
                    'level' => LogLevel::DEBUG,
                ],
            ]);
        }
    ]);
};
