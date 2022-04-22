<?php

namespace FlyingLuscas\Correios;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        error_reporting(E_ALL);
    }
}
