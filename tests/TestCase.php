<?php

namespace TWithers\LaravelAttributes\Tests;

use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;

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
        if (is_null($env = Env::get('APP_ATTRIBUTES_CACHE'))) {
            return app()->bootstrapPath('cache/attributes.php');
        }

        return Str::startsWith($env, ['/', '\\'])
            ? $env
            : app()->basePath($env);
    }
}
