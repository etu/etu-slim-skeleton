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

use App\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            /** @var array $settings */
            $settings = $c->get(SettingsInterface::class)->get('logger');

            $logger = new Logger($settings['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushProcessor(new PsrLogMessageProcessor());
            $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));

            return $logger;
        },
    ]);
};