<?php

namespace TWithers\LaravelAttributes\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
use TWithers\LaravelAttributes\AttributesServiceProvider;

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

    public function getCachedAttributesPath(): string
    {
        return AttributesServiceProvider::getCachedAttributesPath();
    }
}
