<?php

use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeInstance;
use TWithers\LaravelAttributes\Attribute\Entities\AttributeTarget;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

beforeEach(function () {
    $attributeRegistrar = (new AttributeRegistrar())
        ->setAttributes(app('config')->get('attributes.attributes'))
        ->setDirectories(app('config')->get('attributes.directories'));
    $attributeRegistrar->register();
    $this->attributeCollection = $attributeRegistrar->getAttributeCollection();
});

/**
 * Resolve application core configuration implementation.
 */
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

test('the collection is countable', function () {
    expect($this->attributeCollection)->toBeInstanceOf(\Countable::class);
});

test('the collection is populated by registrar', function () {
    expect($this->attributeCollection->all())->toHaveCount(9);
});

test('the collection can find an attribute', function () {
    expect($this->attributeCollection->find(AttributeTarget::TYPE_CLASS, TestClass::class, null)->allAttributes()[0]->instance)
        ->toBeInstanceOf(TestClassAttribute::class);
});

test('the collection can find an attribute by class', function () {
    expect($this->attributeCollection->findByClass(TestClass::class)->allAttributes()[0]->instance)
        ->toBeInstanceOf(TestClassAttribute::class);
});

test('the collection can find an attribute by method', function () {
    expect($this->attributeCollection->findByClassMethod(TestClass::class, 'testMethod')->allAttributes()[0]->instance)
        ->toBeInstanceOf(TestMethodAttribute::class);
});

test('the collection can find an attribute by property', function () {
    expect($this->attributeCollection->findByClassProperty(TestClass::class, 'public')->allAttributes()[0]->instance)
        ->toBeInstanceOf(TestGenericAttribute::class);
});

test('the collection handles serialization', function () {
    $serialized = serialize($this->attributeCollection);
    expect($serialized)->toBeString();

    $newAttributeCollection = unserialize($serialized);

    expect($newAttributeCollection)
        ->toBeInstanceOf(AttributeCollection::class)
        ->toEqual($this->attributeCollection);
    expect($newAttributeCollection)->toHaveCount(count($this->attributeCollection));
});

test('the collection can be added to', function () {
    $originalCount = count($this->attributeCollection);
    $this->attributeCollection->add(AttributeTarget::TYPE_CLASS, 'newMockClass', null, \stdClass::class, new \stdClass());
    expect($this->attributeCollection)->toHaveCount($originalCount + 1);

    $collection = $this->attributeCollection->all();
    $target = end($collection);

    expect($target)
        ->toBeInstanceOf(AttributeTarget::class)
        ->and($target->hasAttribute(\stdClass::class))->toBeTrue()
        ->and($target->findByName(\stdClass::class)[0]->instance)->toBeInstanceOf(\stdClass::class);
});

test('the collection can be added to with attribute instances', function () {
    $originalCount = count($this->attributeCollection);
    $attributeInstance = new AttributeInstance('mockInstance', new \stdClass());
    $this->attributeCollection->addInstance(AttributeTarget::TYPE_CLASS, 'newMockClass', null, $attributeInstance);
    expect($this->attributeCollection)->toHaveCount($originalCount + 1);

    $collection = $this->attributeCollection->all();
    $target = end($collection);

    expect($target)
        ->toBeInstanceOf(AttributeTarget::class)
        ->and($target->hasAttribute('mockInstance'))->toBeTrue()
        ->and($target->findByName('mockInstance')[0]->instance)->toBeInstanceOf(\stdClass::class);
});
