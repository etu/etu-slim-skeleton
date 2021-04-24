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

namespace Tests;

use App\Models\Example;
use PHPUnit\Framework\TestCase;

final class ExampleModelTest extends TestCase
{
    public function testCanCreateModel() : void
    {
        $this->assertInstanceOf(
            Example::class,
            new Example()
        );
    }

    public function testRandomNumberWithinRange() : void
    {
        $model = new Example();
        $model->randomNumber = 47;

        $this->assertEquals(47, $model->randomNumber);
    }
}
