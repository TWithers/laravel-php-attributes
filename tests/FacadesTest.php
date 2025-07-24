<?php

use TWithers\LaravelAttributes\AttributesServiceProvider;
use TWithers\LaravelAttributes\Facades\Attributes;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;

beforeEach(function () {
    if (file_exists(AttributesServiceProvider::getCachedAttributesPath())) {
        unlink(AttributesServiceProvider::getCachedAttributesPath());
    }
    $this->attributesServiceProvider = new AttributesServiceProvider(app());
});

uses()->beforeEach(function () {
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
});

test('the facade works', function () {
    expect(Attributes::all())->toBeArray();
});
