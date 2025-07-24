<?php

use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\AttributesServiceProvider;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;

beforeEach(function () {
    if (file_exists(AttributesServiceProvider::getCachedAttributesPath())) {
        unlink(AttributesServiceProvider::getCachedAttributesPath());
    }
    $this->attributesServiceProvider = new AttributesServiceProvider(app());
});

function usesDefaultConfig()
{
    app('config')->set('attributes.use_cache', false);
    app('config')->set('attributes.directories', [
        'TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1',
        'TWithers\LaravelAttributes\Tests\TestAttributes\Directory2' => __DIR__ . '/TestAttributes/Directory2',
    ]);
    app('config')->set('attributes.attributes', [
        TestClassAttribute::class,
        TestGenericAttribute::class,
        TestMethodAttribute::class,
    ]);
}

function usesCache()
{
    app('config')->set('attributes.use_cache', true);
    app('config')->set('attributes.directories', [
        'TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1',
        'TWithers\LaravelAttributes\Tests\TestAttributes\Directory2' => __DIR__ . '/TestAttributes/Directory2',
    ]);
    app('config')->set('attributes.attributes', [
        TestClassAttribute::class,
        TestGenericAttribute::class,
        TestMethodAttribute::class,
    ]);
}

test('the provider can register the accessor', function () {
    usesDefaultConfig();
    expect(app()->get(AttributeCollection::class))->toBeInstanceOf(AttributeCollection::class);
    expect(app()->get('attributes'))->toBeInstanceOf(AttributeCollection::class);
});

test('the provider uses cache', function () {
    usesCache();
    expect(AttributesServiceProvider::getCachedAttributesPath())->not()->toBeFile();
    $this->attributesServiceProvider->boot();
    expect(AttributesServiceProvider::getCachedAttributesPath())->toBeFile();
});

test('the provider handles invalid cache', function () {
    usesCache();
    $contents = "<?php return 'notvalidserialized';";
    file_put_contents(AttributesServiceProvider::getCachedAttributesPath(), $contents);
    $this->attributesServiceProvider->boot();
    expect(file_get_contents(AttributesServiceProvider::getCachedAttributesPath()))->not()->toBe($contents);
});

test('the provider can disable cache', function () {
    usesDefaultConfig();
    expect(AttributesServiceProvider::getCachedAttributesPath())->not()->toBeFile();
    $attributesServiceProvider = new AttributesServiceProvider(app());
    $attributesServiceProvider->boot();
    expect(AttributesServiceProvider::getCachedAttributesPath())->not()->toBeFile();
});
