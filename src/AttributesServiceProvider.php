<?php

namespace TWithers\LaravelAttributes;

use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use TWithers\LaravelAttributes\Console\AttributesClearCommand;

class AttributesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/attributes.php' => config_path('attributes.php'),
            ], 'config');

            $this->commands([
                AttributesClearCommand::class
            ]);
        }

        $attributes = $this->getAttributes();

        $this->app->singleton(AttributeAccessor::class, function($app) use ($attributes) {
            return new AttributeAccessor($attributes);
        });

        $this->app->bind('attributes', function($app) {
            return $app->get(AttributeAccessor::class);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/attributes.php', 'attributes');
    }


    protected function getAttributes(): array
    {
        if (config('attributes.cache_enabled') && $this->attributesAreCached()) {
            return $this->loadCachedAttributes();
        }

        return $this->loadAttributes();
    }

    protected function loadCachedAttributes(): array
    {
        return require $this->getCachedAttributesPath();
    }

    protected function loadAttributes(): array
    {
        $attributeRegistrar = new AttributeRegistrar($this->getAttributeDirectories(), $this->getAttributeClasses());
        $attributeRegistrar->register();
        $this->cacheAttributes($attributeRegistrar->getAttributeMap());
        return $attributeRegistrar->getAttributeMap();
    }

    protected function getAttributeDirectories(): array
    {
        return config('attributes.directories');
    }

    protected function getAttributeClasses(): array
    {
        return config('attributes.attributes');
    }

    protected function attributesAreCached(): bool
    {
        return $this->app['files']->exists($this->getCachedAttributesPath());
    }

    protected function getCachedAttributesPath(): string
    {
        if (is_null($env = Env::get('APP_ATTRIBUTES_CACHE'))) {
            return $this->app->bootstrapPath('cache/attributes.php');
        }

        return Str::startsWith($env, ['/', '\\'])
            ? $env
            : $this->app->basePath($env);
    }

    protected function cacheAttributes(array $attributesMaps)
    {
        $this->app['files']->put(
            $this->getCachedAttributesPath(), '<?php return '.var_export($attributesMaps, true).';'.PHP_EOL
        );
    }
}
