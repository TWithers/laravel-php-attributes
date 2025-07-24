<?php

use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use TWithers\LaravelAttributes\Attribute\AttributeCollection;
use TWithers\LaravelAttributes\Attribute\AttributeRegistrar;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestClassAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestGenericAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\AttributeClasses\TestMethodAttribute;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\SubDirectory\SubDirectoryClass;
use TWithers\LaravelAttributes\Tests\TestAttributes\Directory1\TestClass;

beforeEach(function () {
    $this->attributeRegistrar = new AttributeRegistrar();
    $this->attributeRegistrar->register();
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

test('the registrar returns an attribute collection', function () {
    $this->attributeRegistrar
        ->setAttributes(app('config')->get('attributes.attributes'))
        ->setDirectories(app('config')->get('attributes.directories'))
        ->register();

    $collection = $this->attributeRegistrar->getAttributeCollection();

    expect($collection)->toBeInstanceOf(AttributeCollection::class);
});

test('the registrar registers class attributes', function () {
    $this->attributeRegistrar
        ->setAttributes(app('config')->get('attributes.attributes'))
        ->setDirectories(app('config')->get('attributes.directories'))
        ->register();

    $collection = $this->attributeRegistrar->getAttributeCollection();

    expect($collection)->toBeInstanceOf(\Countable::class);
    expect($this->attributeRegistrar->getAttributeCollection())->toHaveCount(9);
});

test('the registrar handles invalid paths', function () {
    $exception = null;

    try {
        $this->attributeRegistrar
            ->setAttributes(app('config')->get('attributes.attributes'))
            ->setDirectories(['invalid_namespace' => 'invalid_path'])
            ->register();
    } catch (Exception $e) {
        $exception = $e;
    }
    expect($exception)->not()->toBeInstanceOf(DirectoryNotFoundException::class);
});

test('the registrar handles invalid namespaces', function () {
    $exception = null;

    try {
        $this->attributeRegistrar
            ->setAttributes(app('config')->get('attributes.attributes'))
            ->setDirectories(['invalid_namespace' => __DIR__ . '/TestAttributes/Directory1'])
            ->register();
    } catch (Exception $e) {
        $exception = $e;
    }
    expect($exception)->toBeNull();
});

test('the registrar limits attribute lookups', function () {
    $this->attributeRegistrar
        ->setAttributes([TestClassAttribute::class])
        ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
        ->register();

    $collection = $this->attributeRegistrar->getAttributeCollection();
    $target = $collection->findByClass(TestClass::class);

    expect($target->allAttributes())->toHaveCount(1);
    expect($target->hasAttribute(TestClassAttribute::class))->toBeTrue();
    expect($target->hasAttribute(TestGenericAttribute::class))->toBeFalse();

    $this->attributeRegistrar
        ->setAttributes([TestClassAttribute::class, TestGenericAttribute::class])
        ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
        ->register();

    $collection = $this->attributeRegistrar->getAttributeCollection();
    $target = $collection->findByClass(TestClass::class);

    expect($target->allAttributes())->toHaveCount(2);
    expect($target->hasAttribute(TestClassAttribute::class))->toBeTrue();
    expect($target->hasAttribute(TestGenericAttribute::class))->toBeTrue();
});

test('the registrar registers method attributes', function () {
    $this->attributeRegistrar
        ->setAttributes([TestGenericAttribute::class])
        ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
        ->register();

    $collection = $this->attributeRegistrar->getAttributeCollection();
    $target = $collection->findByClassMethod(TestClass::class, 'testMethod');

    expect($target)->not()->toBeNull();
    expect($target->hasAttribute(TestGenericAttribute::class))->toBeTrue();
});

test('the registrar registers property attributes', function () {
    $this->attributeRegistrar
        ->setAttributes([TestGenericAttribute::class])
        ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
        ->register();

    $collection = $this->attributeRegistrar->getAttributeCollection();
    $target = $collection->findByClassProperty(TestClass::class, 'public');

    expect($target)->not()->toBeNull();
    expect($target->hasAttribute(TestGenericAttribute::class))->toBeTrue();

    $target = $collection->findByClassProperty(TestClass::class, 'protected');

    expect($target)->not()->toBeNull();
    expect($target->hasAttribute(TestGenericAttribute::class))->toBeTrue();

    $target = $collection->findByClassProperty(TestClass::class, 'private');

    expect($target)->not()->toBeNull();
    expect($target->hasAttribute(TestGenericAttribute::class))->toBeTrue();
});

test('the registrar registers subdirectories', function () {
    $this->attributeRegistrar
        ->setAttributes([TestGenericAttribute::class])
        ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory1' => __DIR__ . '/TestAttributes/Directory1'])
        ->register();

    $collection = $this->attributeRegistrar->getAttributeCollection();
    $target = $collection->findByClass(SubDirectoryClass::class);

    expect($target)->not()->toBeNull();
    expect($target->hasAttribute(TestGenericAttribute::class))->toBeTrue();
});

test('the registrar registers repeatable attributes', function () {
    $this->attributeRegistrar
        ->setAttributes([TestGenericAttribute::class])
        ->setDirectories(['TWithers\LaravelAttributes\Tests\TestAttributes\Directory2' => __DIR__ . '/TestAttributes/Directory2'])
        ->register();

    $collection = $this->attributeRegistrar->getAttributeCollection();
    $target = $collection->findByClass(\TWithers\LaravelAttributes\Tests\TestAttributes\Directory2\TestClass::class);

    expect($target)->not()->toBeNull();
    expect($target->hasAttribute(TestGenericAttribute::class))->toBeTrue();
    expect($target->allAttributes())->toHaveCount(2);
});
