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

namespace App\Helpers;

use Psr\Http\Message\ResponseInterface;

class Responses
{
    /**
     * Write data to the response body.
     *
     * This method prepares the response object to return an response to the client.
     *
     * @param ResponseInterface $response Response instance
     * @param mixed $data Mixed data contents
     *
     * @return ResponseInterface
     */
    public static function withData(ResponseInterface $response, $data = null) : ResponseInterface
    {
        $response->getBody()->write((string) json_encode([
            'results' => $data,
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
