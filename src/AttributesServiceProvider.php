<?php

namespace TWithers\LaravelAttributes;

use Illuminate\Support\Env;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
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
                AttributesClearCommand::class,
            ]);
        }

        $attributes = $this->getAttributes();

        $this->app->singleton(AttributeCollection::class, fn () => $attributes);
        $this->app->bind('attributes', fn ($app) => $app->get(AttributeCollection::class));
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/attributes.php', 'attributes');
    }

    protected function getAttributes(): AttributeCollection
    {
        if (config('attributes.use_cache') && $this->attributesAreCached()) {
            if ($collection = $this->loadCachedAttributes()) {
                return $collection;
            }
        }

        return $this->loadAttributes();
    }

    protected function loadCachedAttributes(): ?AttributeCollection
    {
        $serialized = require self::getCachedAttributesPath();
        $data = @unserialize($serialized);
        if ($data === false) {
            unlink(self::getCachedAttributesPath());
            return null;
        }
        return $data;
    }

    protected function loadAttributes(): AttributeCollection
    {
        $attributeRegistrar = new AttributeRegistrar($this->getAttributeDirectories(), $this->getAttributeClasses());
        $attributeRegistrar->register();

        if (config('attributes.use_cache')) {
            $this->cacheAttributes($attributeRegistrar->getAttributeCollection());
        }

        return $attributeRegistrar->getAttributeCollection();
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
        return $this->app['files']->exists(self::getCachedAttributesPath());
    }

    public static function getCachedAttributesPath(): string
    {
        if (is_null($env = Env::get('APP_ATTRIBUTES_CACHE'))) {
            return app()->bootstrapPath('cache/attributes.php');
        }

        return Str::startsWith($env, ['/', '\\'])
            ? $env
            : app()->basePath($env);
    }

    protected function cacheAttributes(AttributeCollection $attributeCollection): void
    {
        $this->app['files']->put(
            self::getCachedAttributesPath(),
            '<?php return '.var_export(serialize($attributeCollection), true).';'.PHP_EOL
        );
    }
}
