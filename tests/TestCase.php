<?php

namespace TWithers\LaravelAttributes\Tests;

use Illuminate\Support\Arr;
use Orchestra\Testbench\TestCase as Orchestra;
use TWithers\LaravelAttributes\AttributeRegistrar;

class TestCase extends Orchestra
{
    protected AttributeRegistrar $attributeRegistrar;

    public function setUp(): void
    {
        parent::setUp();

        $this->attributeRegistrar = (new AttributeRegistrar());
    }

    public function getTestPath(string $directory = null): string
    {
        return __DIR__ . ($directory ? DIRECTORY_SEPARATOR . $directory : '');
    }
}
