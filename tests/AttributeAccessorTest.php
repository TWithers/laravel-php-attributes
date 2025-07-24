<?php

use TWithers\LaravelAttributes\Attribute\AttributeAccessor;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

test('the accessor returns null when no class or property exists', function () {
    $accessor = new AttributeAccessor(AttributeTarget::TYPE_CLASS, 'classDoesntExist', null);
    expect($accessor->reflectForAttributes())->toBeNull();

    $accessor = new AttributeAccessor(AttributeTarget::TYPE_METHOD, TestClass::class, 'methodDoesntExist');
    expect($accessor->reflectForAttributes())->toBeNull();

    $accessor = new AttributeAccessor(AttributeTarget::TYPE_PROPERTY, TestClass::class, 'propertyDoesntExist');
    expect($accessor->reflectForAttributes())->toBeNull();
});

test('the accessor returns attributes when they exist', function () {
    $accessor = new AttributeAccessor(AttributeTarget::TYPE_CLASS, TestClass::class, null);
    expect($accessor->reflectForAttributes())
        ->not()->toBeNull()
        ->toBeArray()
        ->toHaveCount(2);

    $accessor = new AttributeAccessor(AttributeTarget::TYPE_METHOD, TestClass::class, 'testMethod');
    expect($accessor->reflectForAttributes())
        ->not()->toBeNull()
        ->toBeArray()
        ->toHaveCount(2);

    $accessor = new AttributeAccessor(AttributeTarget::TYPE_PROPERTY, TestClass::class, 'public');
    expect($accessor->reflectForAttributes())
        ->not()->toBeNull()
        ->toBeArray()
        ->toHaveCount(1);
});
